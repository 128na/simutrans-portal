<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class FindOrFailWithTrashedTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
    }

    public function test(): void
    {
        $res = $this->userRepository->findOrFailWithTrashed($this->user->id);

        $this->assertInstanceOf(User::class, $res, 'ユーザーが取得できること');
    }

    public function test論理削除(): void
    {
        $this->user->delete();
        $res = $this->userRepository->findOrFailWithTrashed($this->user->id);

        $this->assertInstanceOf(User::class, $res, '削除済みユーザーも取得できること');
    }

    public function test存在しない(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->userRepository->findOrFailWithTrashed(0);
    }
}
