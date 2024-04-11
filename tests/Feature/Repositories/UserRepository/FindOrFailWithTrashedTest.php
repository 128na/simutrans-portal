<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\TestCase;

final class FindOrFailWithTrashedTest extends TestCase
{
    private UserRepository $userRepository;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
        $this->user = User::factory()->create();
    }

    public function test(): void
    {
        $user = $this->userRepository->findOrFailWithTrashed($this->user->id);

        $this->assertInstanceOf(User::class, $user, 'ユーザーが取得できること');
    }

    public function test論理削除(): void
    {
        $this->user->delete();
        $user = $this->userRepository->findOrFailWithTrashed($this->user->id);

        $this->assertInstanceOf(User::class, $user, '削除済みユーザーも取得できること');
    }

    public function test存在しない(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->userRepository->findOrFailWithTrashed(0);
    }
}
