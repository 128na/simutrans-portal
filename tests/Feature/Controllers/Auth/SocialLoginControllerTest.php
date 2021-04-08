<?php

namespace Tests\Feature\Controllers\Auth;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SocialLoginControllerTest extends TestCase
{
    /**
     * @dataProvider dataDriver
     */
    public function testRedirectログイン制限(string $driver)
    {
        Config::set("services.{$driver}.login_restriction", true);
        $url = route('social.login', $driver);
        $res = $this->get($url);
        $res->assertStatus(400);
        $res->assertViewIs('errors.feature_disabled');
    }

    public function dataDriver()
    {
        yield 'google' => ['google'];
        yield 'twitter' => ['twitter'];
    }

    public function testRedirect無効なドライバ()
    {
        $url = route('social.login', 'foo');
        $res = $this->get($url);
        $res->assertStatus(400);
        $res->assertViewIs('errors.feature_disabled');
    }

    public function testRedirectログイン済み()
    {
        $this->actingAs($this->user);
        $url = route('social.login', 'foo');
        $res = $this->get($url);
        $res->assertRedirect(route('mypage.index'));
    }

    /**
     * @dataProvider dataDriver
     */
    public function testCallbackログイン制限(string $driver)
    {
        Config::set("services.{$driver}.login_restriction", true);
        $url = route('social.callback', $driver);
        $res = $this->get($url);
        $res->assertStatus(400);
        $res->assertViewIs('errors.restriction');
    }

    public function testCallback無効なドライバ()
    {
        $url = route('social.callback', 'foo');
        $res = $this->get($url);
        $res->assertStatus(400);
        $res->assertViewIs('errors.restriction');
    }

    public function testCallbackログイン済み()
    {
        $this->actingAs($this->user);
        $url = route('social.callback', 'foo');
        $res = $this->get($url);
        $res->assertRedirect(route('mypage.index'));
    }
}
