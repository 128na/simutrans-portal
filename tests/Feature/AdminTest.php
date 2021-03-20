<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function test未ログインだとログインページへ()
    {
        $url = route('admin.index');
        $response = $this->get($url);
        $response->assertStatus(302);
        $response->assertRedirect(route('mypage.index'));
    }

    public function test管理者のときは表示()
    {
        $url = route('admin.index');
        $this->user->fill(['role' => 'admin'])->save();
        $this->actingAs($this->user);
        $response = $this->get($url);
        $response->assertOk();
    }

    public function test一般ユーザーだと401()
    {
        $url = route('admin.index');
        $this->actingAs($this->user);
        $response = $this->get($url);
        $response->assertStatus(401);
    }
}
