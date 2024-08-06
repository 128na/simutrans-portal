<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Mypage\RedirectController;

use App\Models\Redirect;
use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
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
        $url = '/api/mypage/redirects';
        $this->actingAs($this->user);

        Redirect::create(['user_id' => null, 'from' => 'foo/1', 'to' => 'bar/1']);
        Redirect::create(['user_id' => $this->user->id, 'from' => 'foo/2', 'to' => 'bar/2']);
        Redirect::create(['user_id' => User::factory()->create()->id, 'from' => 'foo/3', 'to' => 'bar/3']);

        $testResponse = $this->getJson($url);
        $testResponse->assertOk();
        $testResponse->assertJsonFragment(['from' => 'foo/2']);
        $testResponse->assertJsonMissing(['from' => 'foo/1']);
        $testResponse->assertJsonMissing(['from' => 'foo/3']);
    }

    public function test未ログイン(): void
    {
        $url = '/api/mypage/redirects';

        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }
}
