<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Tests\Feature\TestCase;

final class FindAllWithTrashedTest extends TestCase
{
    private UserRepository $userRepository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
        $this->user = User::factory()->create();
    }

    public function test(): void
    {
        $res = $this->userRepository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(1, $res, '全てのユーザーが取得できること');
    }

    public function test論理削除(): void
    {
        $this->user->delete();
        $res = $this->userRepository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(1, $res, '削除済みユーザーも取得できること');
    }
}
