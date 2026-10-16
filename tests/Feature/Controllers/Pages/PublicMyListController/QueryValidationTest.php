<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\PublicMyListController;

use App\Models\MyList;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class QueryValidationTest extends TestCase
{
    /**
     * @return array<string, array{string, mixed}>
     */
    public static function invalidQueries(): array
    {
        return [
            'too many results' => ['per_page', 101],
            'zero results' => ['per_page', 0],
            'negative results' => ['per_page', -1],
            'fractional results' => ['per_page', '1.5'],
            'array results' => ['per_page', [20]],
            'zero page' => ['page', 0],
            'negative page' => ['page', -1],
            'fractional page' => ['page', '1.5'],
            'array page' => ['page', [1]],
            'unknown sort column' => ['sort', 'password:desc'],
            'unknown sort direction' => ['sort', 'updated_at:invalid'],
            'array sort' => ['sort', ['updated_at:desc']],
        ];
    }

    #[DataProvider('invalidQueries')]
    public function test_rejects_invalid_queries_for_both_public_endpoints(string $field, mixed $value): void
    {
        $list = MyList::factory()->public()->create();
        $query = http_build_query([$field => $value]);

        foreach (['/api/v1/mylist/public', "/api/v1/mylist/public/{$list->slug}"] as $url) {
            $this->getJson("{$url}?{$query}")
                ->assertUnprocessable()
                ->assertJsonValidationErrors($field);
        }
    }

    public function test_accepts_maximum_page_size_for_both_public_endpoints(): void
    {
        $list = MyList::factory()->public()->create();

        foreach (['/api/v1/mylist/public', "/api/v1/mylist/public/{$list->slug}"] as $url) {
            $this->getJson("{$url}?per_page=100&page=1")
                ->assertOk()
                ->assertJsonPath('meta.per_page', 100);
        }
    }

    public function test_list_sort_is_restricted_to_list_columns(): void
    {
        $this->getJson('/api/v1/mylist/public?sort=position')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort');

        $older = MyList::factory()->public()->create(['updated_at' => now()->subDay()]);
        MyList::factory()->public()->create(['updated_at' => now()]);

        $this->getJson('/api/v1/mylist/public?sort=updated_at:asc&per_page=1')
            ->assertOk()
            ->assertJsonPath('data.0.id', $older->id);
    }

    public function test_item_sort_is_restricted_to_item_columns(): void
    {
        $list = MyList::factory()->public()->create();

        $this->getJson("/api/v1/mylist/public/{$list->slug}?sort=title")
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort');
    }
}
