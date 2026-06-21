<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\PublicMyListController;

use App\Models\MyList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_公開リストはslugで表示できる(): void
    {
        $mylist = MyList::factory()->public()->create();

        $testResponse = $this->get(route('public-mylist.show', ['slug' => $mylist->slug]));

        $testResponse->assertOk();
    }

    public function test_非公開リストのslugは404になる(): void
    {
        $mylist = MyList::factory()->create([
            'is_public' => false,
            'slug' => 'private-list-slug',
        ]);

        $testResponse = $this->get(route('public-mylist.show', ['slug' => $mylist->slug]));

        $testResponse->assertNotFound();
    }
}
