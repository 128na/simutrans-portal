<?php

declare(strict_types=1);

namespace Tests\OldFeature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\TestCase;

class ToggleDeleteTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(UserRepository::class);
    }

    public function testDelete(): void
    {
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'deleted_at' => null,
        ]);

        $this->repository->toggleDelete($this->user);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);
        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
            'deleted_at' => null,
        ]);
    }

    public function testRestore(): void
    {
        $now = now();
        $user = User::factory()->create(['deleted_at' => $now]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => $now,
        ]);

        $this->repository->toggleDelete($user);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }
}
