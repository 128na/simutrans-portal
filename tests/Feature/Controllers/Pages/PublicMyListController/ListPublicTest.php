<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\PublicMyListController;

use App\Models\MyList;
use App\Models\MyListItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

class ListPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_public_lists_only(): void
    {
        $publicOld = MyList::factory()->public()->create([
            'updated_at' => now()->subDays(2),
        ]);
        MyListItem::factory()->count(2)->create(['list_id' => $publicOld->id]);

        $publicNew = MyList::factory()->public()->create([
            'updated_at' => now()->subDay(),
        ]);
        MyListItem::factory()->count(1)->create(['list_id' => $publicNew->id]);

        MyList::factory()->create([
            'updated_at' => now(),
        ]);

        $res = $this->getJson('/api/v1/mylist/public');

        $res->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'note', 'is_public', 'slug', 'items_count', 'created_at', 'updated_at'],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'path', 'per_page', 'to'],
            ]);

        $this->assertCount(2, $res->json('data'));
        $this->assertSame($publicNew->id, $res->json('data.0.id'));
    }

    public function test_respects_pagination_parameters(): void
    {
        MyList::factory()->public()->count(3)->create();

        $res = $this->getJson('/api/v1/mylist/public?per_page=2&page=1');

        $res->assertOk()
            ->assertJsonPath('meta.per_page', 2);
        $this->assertCount(2, $res->json('data'));
    }
}
