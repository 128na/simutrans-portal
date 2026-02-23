<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Models\Article;
use App\Models\MyList;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

class SitemapControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_xml(): void
    {
        // テストデータ作成
        $user = User::factory()->create();
        $tag = Tag::factory()->create();
        $article = Article::factory()->publish()->create(['user_id' => $user->id]);
        $article->tags()->attach($tag);
        $myList = MyList::factory()->public()->create(['user_id' => $user->id]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');

        $xml = $response->getContent();

        // XMLが有効かチェック
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);

        // 主要なURLが含まれているかチェック
        $this->assertStringContainsString('<loc>http://localhost:1080/users</loc>', $xml);
        $this->assertStringContainsString('<loc>http://localhost:1080/tags</loc>', $xml);
        $this->assertStringContainsString('<loc>http://localhost:1080/categories</loc>', $xml);

        // 動的URL
        $userIdentifier = $user->nickname ?? $user->id;
        $this->assertStringContainsString('<loc>http://localhost:1080/users/'.$userIdentifier.'</loc>', $xml);
        $this->assertStringContainsString('<loc>http://localhost:1080/tags/'.urlencode($tag->name).'</loc>', $xml);
        $this->assertStringContainsString('<loc>http://localhost:1080/mylist/'.$myList->slug.'</loc>', $xml);
        $this->assertStringContainsString('<loc>http://localhost:1080/users/'.$userIdentifier.'/'.$article->slug.'</loc>', $xml);
    }

    public function test_sitemap_excludes_unwanted_items(): void
    {
        // 含まれるべきでないデータ作成
        $userWithoutArticles = User::factory()->create();
        // $unpublishedArticle は作成しない
        $privateMyList = MyList::factory()->create(['user_id' => $userWithoutArticles->id, 'is_public' => false]);
        $tagWithoutArticles = Tag::factory()->create();

        // 含まれるべきデータ作成（コントラスト用）
        $userWithArticles = User::factory()->create();
        $publishedArticle = Article::factory()->publish()->create(['user_id' => $userWithArticles->id]);
        $publishedArticle->tags()->attach($tagWithoutArticles); // タグに記事を付ける
        $publicMyList = MyList::factory()->public()->create(['user_id' => $userWithArticles->id]);

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);

        $xml = $response->getContent();

        // 含まれるべきでないものが含まれていないことをチェック
        $this->assertStringNotContainsString('<loc>http://localhost:1080/users/'.($userWithoutArticles->nickname ?? $userWithoutArticles->id).'</loc>', $xml);

        // 含まれるべきものは含まれていることを確認（コントラスト）
        $userIdentifier = $userWithArticles->nickname ?? $userWithArticles->id;
        $this->assertStringContainsString('<loc>http://localhost:1080/users/'.$userIdentifier.'</loc>', $xml);
        $this->assertStringContainsString('<loc>http://localhost:1080/users/'.$userIdentifier.'/'.$publishedArticle->slug.'</loc>', $xml);
        $this->assertStringContainsString('<loc>http://localhost:1080/mylist/'.$publicMyList->slug.'</loc>', $xml);
        $this->assertStringContainsString('<loc>http://localhost:1080/tags/'.urlencode($tagWithoutArticles->name).'</loc>', $xml);
    }
}
