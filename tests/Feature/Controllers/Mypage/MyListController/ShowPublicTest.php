<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\MyList;
use App\Models\MyListItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_public_list_items_successfully(): void
    {
        // Arrange: 公開リストと紐づく公開記事アイテムを作成
        /** @var MyList $list */
        $list = MyList::factory()->public()->create(['slug' => 'test-slug-123']);
        MyListItem::factory()->count(3)->create(['list_id' => $list->id]);

        // Act
        $res = $this->getJson('/api/v1/mylist/public/'.$list->slug);

        // Assert
        $res->assertOk()
            ->assertJsonPath('list.slug', $list->slug)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'note', 'position', 'created_at', 'article' => ['id', 'title']],
                ],
                'list' => ['id', 'title', 'note', 'is_public', 'slug', 'items_count', 'created_at', 'updated_at'],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'path', 'per_page', 'to'],
            ]);

        $this->assertCount(3, $res->json('data'));
    }

    public function test_respects_pagination_parameters(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->public()->create(['slug' => 'page-slug']);
        MyListItem::factory()->count(5)->create(['list_id' => $list->id]);

        $res = $this->getJson('/api/v1/mylist/public/'.$list->slug.'?per_page=2&page=1');

        $res->assertOk()
            ->assertJsonPath('meta.per_page', 2);
        $this->assertCount(2, $res->json('data'));
    }

    public function test_returns_404_when_slug_not_found(): void
    {
        $this->getJson('/api/v1/mylist/public/not-exists-slug')->assertNotFound();
    }

    public function test_returns_404_when_list_is_private(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create(['slug' => 'private-slug', 'is_public' => false]);

        $this->getJson('/api/v1/mylist/public/'.$list->slug)->assertNotFound();
    }
}
