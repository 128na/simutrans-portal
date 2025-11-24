<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\Article;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

final class UpdateTest extends TestCase
{
    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->addonIntroduction()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/articles/'.$this->article->id;

        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test_更新通知する(): void
    {
        Notification::fake();
        $url = '/api/v2/articles/'.$this->article->id;
        $oldModifiedAt = $this->article->modified_at->toAtomString();

        $this->actingAs($this->article->user);

        $testResponse = $this->postJson($url, [
            'article' => $this->createArticle(),
            'should_notify' => true,
            'without_update_modified_at' => false,
        ]);
        $testResponse->assertStatus(200);

        $this->article->refresh();
        $this->assertNotEquals(
            $this->article->modified_at->toAtomString(),
            $oldModifiedAt,
            '更新日が更新されていること'
        );
        Notification::assertSentTo(
            $this->article,
            SendArticleUpdated::class
        );
    }

    public function test_更新通知しない(): void
    {
        Notification::fake();
        $url = '/api/v2/articles/'.$this->article->id;
        $oldModifiedAt = $this->article->modified_at->toAtomString();

        $this->actingAs($this->article->user);

        $testResponse = $this->postJson($url, [
            'article' => $this->createArticle(),
            'should_notify' => false,
            'without_update_modified_at' => true,
        ]);
        $testResponse->assertStatus(200);

        $this->article->refresh();
        $this->assertEquals(
            $this->article->modified_at->toAtomString(),
            $oldModifiedAt,
            '更新日が更新されていないこと'
        );
        Notification::assertNotSentTo(
            $this->article,
            SendArticleUpdated::class
        );
    }

    public function test_初めて公開になるときは更新でなく投稿通知(): void
    {
        Notification::fake();
        $url = '/api/v2/articles/'.$this->article->id;
        $this->article->update(['published_at' => null]);

        $this->actingAs($this->article->user);

        $testResponse = $this->postJson($url, [
            'article' => $this->createArticle(),
            'should_notify' => true,
            'without_update_modified_at' => false,
        ]);
        $testResponse->assertStatus(200);

        $this->article->refresh();
        Notification::assertSentTo(
            $this->article,
            SendArticlePublished::class
        );
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
