<?php

namespace Tests\Feature\Command;

use App\Models\Article;
use App\Models\User;
use App\Notifications\DeadLinkDetected;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TestCheckDeadlink extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testCommand()
    {
        Notification::fake();
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'addon-introduction',
            'status' => 'publish',
            'contents' => [
                'link' => config('app.url') . '/not_found_url',
                'exclude_link_check' => false,
            ],
        ]);
        $res = $this->get($article->contents->link);
        $res->assertNotFound();

        Notification::assertNothingSent();
        $this->artisan('check:deadlink')
            ->assertExitCode(0);
        Notification::assertSentTo($article, DeadLinkDetected::class);
    }

    public function testExclude()
    {
        Notification::fake();
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'addon-introduction',
            'status' => 'publish',
            'contents' => [
                'link' => config('app.url') . '/not_found_url',
                'exclude_link_check' => true,
            ],
        ]);
        $res = $this->get($article->contents->link);
        $res->assertNotFound();

        Notification::assertNothingSent();
        $this->artisan('check:deadlink')
            ->assertExitCode(0);
        Notification::assertNothingSent();
    }

    public function testTrash()
    {
        Notification::fake();
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'addon-introduction',
            'status' => 'trash',
            'contents' => [
                'link' => config('app.url') . '/not_found_url',
                'exclude_link_check' => false,
            ],
        ]);
        $res = $this->get($article->contents->link);
        $res->assertNotFound();

        Notification::assertNothingSent();
        $this->artisan('check:deadlink')
            ->assertExitCode(0);
        Notification::assertNothingSent();
    }

    public function testDraft()
    {
        Notification::fake();
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'addon-introduction',
            'status' => 'draft',
            'contents' => [
                'link' => config('app.url') . '/not_found_url',
                'exclude_link_check' => false,
            ],
        ]);
        $res = $this->get($article->contents->link);
        $res->assertNotFound();

        Notification::assertNothingSent();
        $this->artisan('check:deadlink')
            ->assertExitCode(0);
        Notification::assertNothingSent();
    }

    public function testPrivate()
    {
        Notification::fake();
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'addon-introduction',
            'status' => 'private',
            'contents' => [
                'link' => config('app.url') . '/not_found_url',
                'exclude_link_check' => false,
            ],
        ]);
        $res = $this->get($article->contents->link);
        $res->assertNotFound();

        Notification::assertNothingSent();
        $this->artisan('check:deadlink')
            ->assertExitCode(0);
        Notification::assertNothingSent();
    }
}
