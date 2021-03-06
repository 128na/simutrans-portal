<?php

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class FindOrFailWithTrashedTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
    }

    public function test()
    {
        $res = $this->repository->findOrFailWithTrashed($this->user->id);

        $this->assertInstanceOf(User::class, $res, 'ユーザーが取得できること');
    }

    public function test論理削除()
    {
        $this->user->delete();
        $res = $this->repository->findOrFailWithTrashed($this->user->id);

        $this->assertInstanceOf(User::class, $res, '削除済みユーザーも取得できること');
    }

    public function test存在しない()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFailWithTrashed(0);
    }
}
