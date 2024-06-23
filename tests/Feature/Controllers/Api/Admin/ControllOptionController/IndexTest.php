<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\ControllOptionController;

use App\Enums\UserRole;
use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/admin/controll_options';

        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test_管理者(): void
    {
        $url = '/api/admin/controll_options';

        $this->actingAs($this->user);
        $testResponse = $this->getJson($url);
        $testResponse->assertOk();
    }

    public function test_管理者以外(): void
    {
        $url = '/api/admin/controll_options';

        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }
}
