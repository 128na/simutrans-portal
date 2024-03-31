<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\TestCase;

class FindOrFailWithTrashedTest extends TestCase
{
    private UserRepository $repository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
        $this->user = User::factory()->create();
    }

    public function test(): void
    {
        $user = $this->repository->findOrFailWithTrashed($this->user->id);

        $this->assertInstanceOf(User::class, $user, 'ユーザーが取得できること');
    }

    public function test論理削除(): void
    {
        $this->user->delete();
        $user = $this->repository->findOrFailWithTrashed($this->user->id);

        $this->assertInstanceOf(User::class, $user, '削除済みユーザーも取得できること');
    }

    public function test存在しない(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFailWithTrashed(0);
    }
}
