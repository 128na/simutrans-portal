<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\PublicMyListController;

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
}
