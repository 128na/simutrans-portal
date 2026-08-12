<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\AccountController;

use App\Models\User;
use Tests\Feature\TestCase;

class DestroyTest extends TestCase
{
    public function test_未ログイン(): void
    {
        $testResponse = $this->deleteJson(route('mypage.account.destroy'), ['current_password' => 'password']);

        $testResponse->assertUnauthorized();
    }

    public function test_パスワードが違う場合は削除されない(): void
    {
        $user = User::factory()->create();

        $testResponse = $this->actingAs($user)
            ->deleteJson(route('mypage.account.destroy'), ['current_password' => 'wrong-password']);

        $testResponse->assertUnprocessable();
        $testResponse->assertJsonValidationErrors('current_password');
        $this->assertNull($user->fresh()?->deleted_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_正しいパスワードで退会できる(): void
    {
        $user = User::factory()->create();

        $testResponse = $this->actingAs($user)
            ->deleteJson(route('mypage.account.destroy'), ['current_password' => 'password']);

        $testResponse->assertOk();
        $this->assertNotNull($user->fresh()?->deleted_at);
        $this->assertGuest();
    }
}
