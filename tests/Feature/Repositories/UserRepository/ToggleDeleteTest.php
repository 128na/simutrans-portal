<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\TestCase;

class ToggleDeleteTest extends TestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
    }

    public function testDelete(): void
    {
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'deleted_at' => null,
        ]);

        $this->userRepository->toggleDelete($this->user);

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

        $this->userRepository->toggleDelete($user);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }
}
