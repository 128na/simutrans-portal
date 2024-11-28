<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Tests\Feature\TestCase;

final class ToggleDeleteTest extends TestCase
{
    private UserRepository $userRepository;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
        $this->user = User::factory()->create();
    }

    public function test_delete(): void
    {
        $this->assertFalse($this->user->trashed(), '削除されていない');

        $this->userRepository->toggleDelete($this->user);

        $this->assertTrue($this->user->fresh()->trashed(), '削除されている');
    }

    public function test_restore(): void
    {
        $this->user->delete();
        $this->assertTrue($this->user->fresh()->trashed(), '削除されている');

        $this->userRepository->toggleDelete($this->user);

        $this->assertFalse($this->user->fresh()->trashed(), '削除されていない');
    }
}
