<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\TestCase;

class FindByEmailWithTrashedTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
    }

    public function test(): void
    {
        $res = $this->userRepository->findByEmailWithTrashed($this->user->email);

        $this->assertInstanceOf(User::class, $res, 'ユーザーが取得できること');
    }

    public function test論理削除(): void
    {
        $this->user->delete();
        $res = $this->userRepository->findByEmailWithTrashed($this->user->email);

        $this->assertInstanceOf(User::class, $res, '削除済みユーザーも取得できること');
    }

    public function test存在しない(): void
    {
        $res = $this->userRepository->findByEmailWithTrashed('test');

        $this->assertNull($res);
    }
}
