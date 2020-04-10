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
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'contents' => [
                'link' => config('app.url') . '/not_found_url',
            ],
        ]);
        $res = $this->get($article->contents->link);
        $res->assertNotFound();

        Notification::assertNothingSent();
        $this->artisan('check:deadlink')
            ->assertExitCode(0);
        Notification::assertSentTo($user, DeadLinkDetected::class);
    }
}
