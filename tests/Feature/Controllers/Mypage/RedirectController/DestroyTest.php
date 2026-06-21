<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\RedirectController;

use App\Models\Redirect;
use App\Models\User;
use Tests\Feature\TestCase;

class DestroyTest extends TestCase
{
    public function test(): void
    {
        $user = User::factory()->create();
        /** @var Redirect $redirect */
        $redirect = Redirect::factory()->create(['user_id' => $user->id]);

        $testResponse = $this->actingAs($user)->delete(route('mypage.redirects.destroy', $redirect));

        $testResponse->assertRedirect(route('mypage.redirects'));
        $this->assertDatabaseMissing('redirects', ['id' => $redirect->id]);
    }

    public function test_未ログイン(): void
    {
        /** @var Redirect $redirect */
        $redirect = Redirect::factory()->create();

        $testResponse = $this->delete(route('mypage.redirects.destroy', $redirect));

        $testResponse->assertRedirect(route('login'));
        $this->assertDatabaseHas('redirects', ['id' => $redirect->id]);
    }

    public function test_他人のリダイレクトは削除できない(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        /** @var Redirect $redirect */
        $redirect = Redirect::factory()->create(['user_id' => $owner->id]);

        $testResponse = $this->actingAs($otherUser)->delete(route('mypage.redirects.destroy', $redirect));

        $testResponse->assertForbidden();
        $this->assertDatabaseHas('redirects', ['id' => $redirect->id]);
    }
}
