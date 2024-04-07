<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\Feature\TestCase;

final class ToggleDeleteTest extends TestCase
{
    private UserRepository $repository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
        $this->user = User::factory()->create();
    }

    public function testDelete(): void
    {
        $this->assertFalse($this->user->trashed(), '削除されていない');

        $this->repository->toggleDelete($this->user);

        $this->assertTrue($this->user->fresh()->trashed(), '削除されている');
    }

    public function testRestore(): void
    {
        $this->user->delete();
        $this->assertTrue($this->user->fresh()->trashed(), '削除されている');

        $this->repository->toggleDelete($this->user);

        $this->assertFalse($this->user->fresh()->trashed(), '削除されていない');
    }
}
