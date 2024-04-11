<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use App\Enums\UserRole;
use App\Models\User;
use Tests\Feature\TestCase;

final class AdminControllerTest extends TestCase
{
    private string $url;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('admin.index');
        $this->user = User::factory()->create();
    }

    public function testGuest(): void
    {
        $testResponse = $this->get($this->url);

        $testResponse->assertRedirect(route('mypage.index'));
    }

    public function testUser(): void
    {
        $this->actingAs($this->user);
        $testResponse = $this->get($this->url);
        $testResponse->assertUnauthorized();
    }

    public function testAdmin(): void
    {
        $this->user->update(['role' => UserRole::Admin]);
        $this->actingAs($this->user);
        $testResponse = $this->get($this->url);

        $testResponse->assertOk();
    }
}
