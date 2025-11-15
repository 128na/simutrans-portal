<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\ArticleController;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use App\Notifications\SendArticlePublished;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

final class StoreTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/articles';

        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test_投稿通知する(): void
    {
        Notification::fake();
        $url = '/api/v2/articles';

        $this->actingAs($this->user);

        $testResponse = $this->postJson($url, [
            'article' => $this->createArticle(),
            'should_notify' => true,
        ]);
        $testResponse->assertStatus(200);

        $article = Article::find($testResponse->json('article_id'));
        Notification::assertSentTo($article, SendArticlePublished::class);
    }

    public function test_投稿通知しない(): void
    {
        Notification::fake();
        $url = '/api/v2/articles';

        $this->actingAs($this->user);

        $testResponse = $this->postJson($url, [
            'article' => $this->createArticle(),
            'should_notify' => false,
        ]);
        $testResponse->assertStatus(200);

        $article = Article::find($testResponse->json('article_id'));
        Notification::assertNotSentTo($article, SendArticlePublished::class);
    }

    private function createArticle(): array
    {
        return [
            'post_type' => ArticlePostType::AddonIntroduction->value,
            'status' => ArticleStatus::Publish->value,
            'title' => 'test title ',
            'slug' => 'test-slug',
            'contents' => [
                'thumbnail' => null,
                'author' => 'test author',
                'link' => 'http://example.com',
                'description' => 'test description',
                'thanks' => 'test thanks',
                'license' => 'test license',
                'agreement' => true,
            ],
            'tags' => [],
            'categories' => [],
            'articles' => [],
            'published_at' => null,
        ];
    }
}
