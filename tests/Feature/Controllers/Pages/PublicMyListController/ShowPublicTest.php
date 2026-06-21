<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\PublicMyListController;

use App\Models\MyList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

class ShowPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_公開リストはslugで表示できる(): void
    {
        $mylist = MyList::factory()->public()->create();

        $res = $this->getJson("/api/v1/mylist/public/{$mylist->slug}");

        $res->assertOk();
        $res->assertJsonPath('list.id', $mylist->id);
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
