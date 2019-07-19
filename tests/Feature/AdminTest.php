<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testNeedLogin()
    {
        $response = $this->get('mypage');
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $user = factory(User::class)->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertOk();
        $response = $this->get('/admin');
        $response->assertOk();
    }

    public function testError()
    {
        $response = $this->get('/admin/error');
        $response->assertRedirect('login');
        $user = factory(User::class)->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/admin/error');
        $response->assertStatus(500);
    }

    public function testWarnig()
    {
        $response = $this->get('/admin/warning');
        $response->assertRedirect('login');
        $user = factory(User::class)->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/admin/warning');
        $response->assertStatus(500);
    }

    public function testNotice()
    {
        $response = $this->get('/admin/notice');
        $response->assertRedirect('login');
        $user = factory(User::class)->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/admin/notice');
        $response->assertStatus(500);
    }

}
