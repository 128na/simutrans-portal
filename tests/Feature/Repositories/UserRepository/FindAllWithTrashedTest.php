<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Tests\TestCase;

class FindAllWithTrashedTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
    }

    public function test(): void
    {
        $res = $this->userRepository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(2, $res, '全てのユーザーが取得できること');
    }

    public function test論理削除(): void
    {
        $this->user->delete();
        $res = $this->userRepository->findAllWithTrashed();

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(2, $res, '削除済みユーザーも取得できること');
    }
}
