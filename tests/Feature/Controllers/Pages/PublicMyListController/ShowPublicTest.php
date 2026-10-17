<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\PublicMyListController;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\MyList;
use App\Models\MyListItem;
use Tests\Feature\TestCase;

class ShowPublicTest extends TestCase
{
    public function test_公開リストはslugで表示できる(): void
    {
        $mylist = MyList::factory()->public()->create();

        $res = $this->getJson("/api/v1/mylist/public/{$mylist->slug}");

        $res->assertOk();
        $res->assertJsonPath('list.id', $mylist->id);
    }

    public function test_記事urlは日本語スラッグでも二重エンコードされない(): void
    {
        $mylist = MyList::factory()->public()->create();
        $article = Article::factory()->publish()->create([
            'title' => '日本語のマイリスト記事',
            'slug' => '日本語のマイリスト記事',
        ]);
        MyListItem::factory()->create([
            'list_id' => $mylist->id,
            'article_id' => $article->id,
        ]);

        $res = $this->getJson("/api/v1/mylist/public/{$mylist->slug}");

        $res->assertOk();
        $url = $res->json('data.0.article.url');
        $this->assertIsString($url);
        $this->assertStringNotContainsString('%25', $url);
        $this->assertSame($article->showUrl(), $url);
    }

    public function test_非公開リストのslugは404になる(): void
    {
        $mylist = MyList::factory()->create([
            'is_public' => false,
            'slug' => 'private-list-slug',
        ]);

        $res = $this->getJson("/api/v1/mylist/public/{$mylist->slug}");

        $res->assertNotFound();
    }

    public function test_public_list_excludes_every_nonpublic_article_status(): void
    {
        $mylist = MyList::factory()->public()->create();
        $published = null;
        foreach (ArticleStatus::cases() as $status) {
            $article = Article::factory()->create(['status' => $status]);
            MyListItem::factory()->create(['list_id' => $mylist->id, 'article_id' => $article->id]);
            if ($status === ArticleStatus::Publish) {
                $published = $article;
            }
        }
        $response = $this->getJson("/api/v1/mylist/public/{$mylist->slug}")->assertOk()->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.article.id', $published->id);
    }

    public function test_public_list_removes_articles_after_unpublishing_or_soft_deletion(): void
    {
        $mylist = MyList::factory()->public()->create();
        $drafted = Article::factory()->publish()->create();
        $deleted = Article::factory()->publish()->create();
        foreach ([$drafted, $deleted] as $article) {
            MyListItem::factory()->create(['list_id' => $mylist->id, 'article_id' => $article->id]);
        }
        $url = "/api/v1/mylist/public/{$mylist->slug}";
        $this->getJson($url)->assertOk()->assertJsonCount(2, 'data');
        $drafted->update(['status' => ArticleStatus::Draft]);
        $deleted->delete();
        $this->getJson($url)->assertOk()->assertJsonCount(0, 'data');
    }
}
