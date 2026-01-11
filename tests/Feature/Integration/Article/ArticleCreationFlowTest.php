<?php

declare(strict_types=1);

namespace Tests\Feature\Integration\Article;

use App\Actions\Article\StoreArticle;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

/**
 * 記事作成フロー統合テスト
 * 記事作成 → タグ付け → カテゴリ設定 → 公開の一連の流れを検証
 */
class ArticleCreationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 完全な記事作成フローが正常に動作する
     */
    public function testCompleteArticleCreationFlow(): void
    {
        // ユーザー、カテゴリ、タグを作成
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'Tag1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag2']);

        // 記事データ準備
        $data = [
            'should_notify' => false,
            'article' => [
                'status' => ArticleStatus::Publish->value,
                'title' => 'Test Article',
                'slug' => 'test-article',
                'post_type' => ArticlePostType::Page->value,
                'contents' => [
                    'sections' => [
                        ['type' => 'text', 'text' => 'Test content'],
                    ],
                ],
                'categories' => [$category->id],
                'tags' => [$tag1->id, $tag2->id],
            ],
        ];

        // 記事作成
        $storeAction = app(StoreArticle::class);
        $article = $storeAction($user, $data);

        // 記事が作成されたことを確認
        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals('Test Article', $article->title);
        $this->assertEquals(ArticleStatus::Publish, $article->status);
        $this->assertEquals($user->id, $article->user_id);

        // カテゴリが関連付けられていることを確認
        $this->assertCount(1, $article->categories);
        $this->assertEquals($category->id, $article->categories->first()->id);

        // タグが関連付けられていることを確認
        $this->assertCount(2, $article->tags);
        $tagNames = $article->tags->pluck('name')->toArray();
        $this->assertContains('Tag1', $tagNames);
        $this->assertContains('Tag2', $tagNames);

        // DBに保存されていることを確認
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Test Article',
            'status' => ArticleStatus::Publish->value,
        ]);
    }

    /**
     * @test
     * 下書き記事の作成とその後の公開
     */
    public function testDraftArticleThenPublish(): void
    {
        $user = User::factory()->create();

        // 下書き記事作成
        $draftData = [
            'should_notify' => false,
            'article' => [
                'status' => ArticleStatus::Draft->value,
                'title' => 'Draft Article',
                'slug' => 'draft-article',
                'post_type' => ArticlePostType::Markdown->value,
                'contents' => ['markdown' => '# Draft content'],
            ],
        ];

        $storeAction = app(StoreArticle::class);
        $article = $storeAction($user, $draftData);

        // 下書き状態を確認
        $this->assertEquals(ArticleStatus::Draft, $article->status);
        $this->assertNull($article->published_at);

        // 公開に変更
        $updateAction = app(\App\Actions\Article\UpdateArticle::class);
        $publishData = [
            'should_notify' => false,
            'should_update_modified' => true,
            'article' => [
                'status' => ArticleStatus::Publish->value,
                'title' => 'Published Article',
                'slug' => 'published-article',
                'post_type' => ArticlePostType::Markdown->value,
                'contents' => ['markdown' => '# Published content'],
            ],
        ];

        $updatedArticle = $updateAction($article, $publishData);

        // 公開状態を確認
        $this->assertEquals(ArticleStatus::Publish, $updatedArticle->status);
        $this->assertNotNull($updatedArticle->published_at);
        $this->assertEquals('Published Article', $updatedArticle->title);
    }

    /**
     * @test
     * 複数カテゴリとタグの関連付け
     */
    public function testMultipleCategoriesAndTags(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory()->count(3)->create();
        $tags = Tag::factory()->count(5)->create();

        $data = [
            'should_notify' => false,
            'article' => [
                'status' => ArticleStatus::Publish->value,
                'title' => 'Multi Category Article',
                'slug' => 'multi-category',
                'post_type' => ArticlePostType::Page->value,
                'contents' => ['sections' => []],
                'categories' => $categories->pluck('id')->toArray(),
                'tags' => $tags->pluck('id')->toArray(),
            ],
        ];

        $storeAction = app(StoreArticle::class);
        $article = $storeAction($user, $data);

        // 全カテゴリが関連付けられていることを確認
        $this->assertCount(3, $article->fresh()->categories);

        // 全タグが関連付けられていることを確認
        $this->assertCount(5, $article->fresh()->tags);
    }

    /**
     * @test
     * 既存タグと新規タグの混在
     */
    public function testMixedExistingAndNewTags(): void
    {
        $user = User::factory()->create();
        $tag1 = Tag::factory()->create(['name' => 'Tag1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag2']);
        $tag3 = Tag::factory()->create(['name' => 'Tag3']);

        $data = [
            'should_notify' => false,
            'article' => [
                'status' => ArticleStatus::Publish->value,
                'title' => 'Multiple Tags Article',
                'slug' => 'multiple-tags',
                'post_type' => ArticlePostType::Page->value,
                'contents' => ['sections' => []],
                'tags' => [$tag1->id, $tag2->id, $tag3->id],
            ],
        ];

        $storeAction = app(StoreArticle::class);
        $article = $storeAction($user, $data);

        // 3つのタグが関連付けられていることを確認
        $this->assertCount(3, $article->fresh()->tags);

        // タグが使用されていることを確認
        $tagIds = $article->fresh()->tags->pluck('id')->toArray();
        $this->assertContains($tag1->id, $tagIds);
        $this->assertContains($tag2->id, $tagIds);
        $this->assertContains($tag3->id, $tagIds);
    }

    /**
     * @test
     * 予約投稿の作成
     */
    public function testScheduledArticleCreation(): void
    {
        $user = User::factory()->create();
        $futureDate = now()->addDays(7)->toDateTimeString();

        $data = [
            'should_notify' => false,
            'article' => [
                'status' => ArticleStatus::Reservation->value,
                'title' => 'Scheduled Article',
                'slug' => 'scheduled',
                'post_type' => ArticlePostType::Page->value,
                'contents' => ['sections' => []],
                'published_at' => $futureDate,
            ],
        ];

        $storeAction = app(StoreArticle::class);
        $article = $storeAction($user, $data);

        // 予約状態を確認
        $this->assertEquals(ArticleStatus::Reservation, $article->status);
        $this->assertEquals($futureDate, $article->published_at->toDateTimeString());
    }
}
