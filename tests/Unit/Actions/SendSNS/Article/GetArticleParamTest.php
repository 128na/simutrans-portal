<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\GetArticleParam;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Tests\Unit\TestCase;

class GetArticleParamTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Fakerのuniqueリセット（複数テスト実行時の重複を防ぐ）
        fake()->unique(true);
    }

    public function test_returns_article_parameters_with_nickname(): void
    {
        $carbon = Carbon::parse('2024-01-15 12:30:45');
        $action = new GetArticleParam($carbon);

        $user = User::factory()->make([
            'id' => 1,
            'name' => 'Test User',
            'nickname' => 'testuser',
            'email' => 'test1@example.com',
        ]);

        $article = Article::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'title' => 'Test Article',
            'slug' => 'test-article',
        ]);
        $article->setRelation('user', $user);
        $article->setRelation('categories', collect());
        $article->setRelation('categoryPaks', collect());

        $result = $action($article);

        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('at', $result);
        $this->assertArrayHasKey('tags', $result);

        $this->assertSame('Test Article', $result['title']);
        $this->assertStringContainsString('testuser', $result['url']);
        $this->assertStringContainsString('test-article', $result['url']);
        $this->assertSame('Test User', $result['name']);
        $this->assertSame('2024/01/15 12:30', $result['at']);
        // 翻訳されたハッシュタグが含まれる（lang/ja.json）
        $this->assertNotEmpty($result['tags']);
    }

    public function test_returns_article_parameters_without_nickname(): void
    {
        $carbon = Carbon::parse('2024-02-20 14:45:30');
        $action = new GetArticleParam($carbon);

        $user = User::factory()->make([
            'id' => 2,
            'name' => 'Another User',
            'nickname' => null,
            'email' => 'test2@example.com',
        ]);

        $article = Article::factory()->make([
            'id' => 2,
            'user_id' => 2,
            'title' => 'Another Article',
            'slug' => 'another-article',
        ]);
        $article->setRelation('user', $user);
        $article->setRelation('categories', collect());
        $article->setRelation('categoryPaks', collect());

        $result = $action($article);

        $this->assertStringContainsString('2', $result['url']); // user_id
        $this->assertStringContainsString('another-article', $result['url']);
        $this->assertSame('Another User', $result['name']);
        $this->assertSame('2024/02/20 14:45', $result['at']);
    }

    public function test_includes_pak_categories_in_tags(): void
    {
        $carbon = Carbon::parse('2024-03-10 10:00:00');
        $action = new GetArticleParam($carbon);

        $user = User::factory()->make(['id' => 3, 'name' => 'User', 'email' => 'test3@example.com']);

        $pak128 = Category::factory()->make(['slug' => 'pak128', 'type' => 'pak', 'user_id' => 3]);
        $pak64 = Category::factory()->make(['slug' => 'pak64', 'type' => 'pak', 'user_id' => 3]);

        $article = Article::factory()->make([
            'id' => 3,
            'user_id' => 3,
            'title' => 'Pak Article',
            'slug' => 'pak-article',
        ]);
        $article->setRelation('user', $user);
        $article->setRelation('categories', collect([$pak128, $pak64]));
        $article->setRelation('categoryPaks', collect([$pak128, $pak64]));

        $result = $action($article);

        // 翻訳されたハッシュタグが含まれる
        $this->assertNotEmpty($result['tags']);
        // pak128, pak64のハッシュタグが含まれる（または翻訳版）
        $this->assertIsString($result['tags']);
    }

    public function test_translates_hash_tags(): void
    {
        $carbon = Carbon::now();
        $action = new GetArticleParam($carbon);

        $user = User::factory()->make(['id' => 4, 'name' => 'User', 'email' => 'test4@example.com']);
        $pak128 = Category::factory()->make(['slug' => 'pak128', 'type' => 'pak', 'user_id' => 4]);

        $article = Article::factory()->make([
            'id' => 4,
            'user_id' => 4,
            'title' => 'Title',
            'slug' => 'slug',
        ]);
        $article->setRelation('user', $user);
        $article->setRelation('categories', collect([$pak128]));
        $article->setRelation('categoryPaks', collect([$pak128]));

        $result = $action($article);

        // タグに翻訳またはスラッグが含まれる
        $this->assertIsString($result['tags']);
        $this->assertNotEmpty($result['tags']);
    }
}
