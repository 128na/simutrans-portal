<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\Feature\TestCase;

final class FindByEmailWithTrashedTest extends TestCase
{
    private User $user;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
        $this->user = User::factory()->create();
    }

    public function test(): void
    {
        $res = $this->repository->findByEmailWithTrashed($this->user->email);

        $this->assertInstanceOf(User::class, $res, 'ユーザーが取得できること');
    }

    public function test論理削除(): void
    {
        $this->user->delete();
        $res = $this->repository->findByEmailWithTrashed($this->user->email);

        $this->assertInstanceOf(User::class, $res, '削除済みユーザーも取得できること');
    }

    public function test存在しない(): void
    {
        $res = $this->repository->findByEmailWithTrashed('test');

        $this->assertNull($res);
    }
}
