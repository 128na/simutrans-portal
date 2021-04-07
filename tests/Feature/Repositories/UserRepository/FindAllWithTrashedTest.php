<?php

namespace Tests\Feature\Repositories\UserRepository;

use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FindAllWithTrashedTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
    }

    public function test()
    {
        $res = $this->repository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(2, $res, '全てのユーザーが取得できること');
    }

    public function test論理削除()
    {
        $this->user->delete();
        $res = $this->repository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(2, $res, '削除済みユーザーも取得できること');
    }
}
