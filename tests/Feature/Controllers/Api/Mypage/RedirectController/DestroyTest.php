<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\RedirectController;

use App\Models\Redirect;
use App\Models\User;
use Tests\Feature\TestCase;

final class DestroyTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test(): void
    {
        $this->actingAs($this->user);

        $redirect = Redirect::create(['user_id' => $this->user->id, 'from' => 'foo/2', 'to' => 'bar/2']);
        $url = '/api/mypage/redirects/'.$redirect->id;

        $testResponse = $this->deleteJson($url);
        $testResponse->assertOk();
        $testResponse->assertJsonMissing(['from' => 'foo/2']);
    }

    public function test_userがNull(): void
    {
        $this->actingAs($this->user);

        $redirect = Redirect::create(['user_id' => null, 'from' => 'foo/1', 'to' => 'bar/1']);
        $url = '/api/mypage/redirects/'.$redirect->id;

        $testResponse = $this->deleteJson($url);
        $testResponse->assertForbidden();
    }

    public function test_他人(): void
    {
        $this->actingAs($this->user);

        $redirect = Redirect::create(['user_id' => User::factory()->create()->id, 'from' => 'foo/3', 'to' => 'bar/3']);
        $url = '/api/mypage/redirects/'.$redirect->id;

        $testResponse = $this->deleteJson($url);
        $testResponse->assertForbidden();
    }

    public function test未ログイン(): void
    {
        $redirect = Redirect::create(['user_id' => $this->user->id, 'from' => 'foo/2', 'to' => 'bar/2']);
        $url = '/api/mypage/redirects/'.$redirect->id;

        $testResponse = $this->deleteJson($url);
        $testResponse->assertUnauthorized();
    }
}
