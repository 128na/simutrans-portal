<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use App\Repositories\MyListItemRepository;
use App\Repositories\MyListRepository;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class MyListSortDirectionTest extends TestCase
{
    public static function sortingMethods(): array
    {
        return [
            'user lists' => ['paginateForUser'],
            'public lists' => ['paginatePublic'],
            'list items' => ['paginateForList'],
            'public list items' => ['paginatePublicForList'],
        ];
    }

    #[DataProvider('sortingMethods')]
    public function test_sorting_accepts_uppercase_directions(string $method): void
    {
        $user = User::factory()->create();
        $lists = MyList::factory()->count(2)->create(['user_id' => $user->id, 'is_public' => true]);
        $list = $lists->first();
        $items = MyListItem::factory()->count(2)->create([
            'list_id' => $list->id,
        ]);
        $repository = str_contains($method, 'ForList') ? app(MyListItemRepository::class) : app(MyListRepository::class);
        $arguments = match ($method) {
            'paginateForUser' => [$user],
            'paginatePublic' => [],
            default => [$list],
        };
        $expectedIds = (str_contains($method, 'ForList') ? $items : $lists)->pluck('id')->sort()->values()->all();

        foreach (['ASC', 'DESC'] as $direction) {
            $result = $repository->$method(...[...$arguments, 1, 20, 'id', $direction]);
            $this->assertSame($direction === 'ASC' ? $expectedIds : array_reverse($expectedIds), collect($result->items())->pluck('id')->all());
        }
    }

    #[DataProvider('sortingMethods')]
    public function test_sorting_rejects_invalid_direction(string $method): void
    {
        $repository = str_contains($method, 'ForList') ? app(MyListItemRepository::class) : app(MyListRepository::class);
        $arguments = match ($method) {
            'paginateForUser' => [User::factory()->create()],
            'paginatePublic' => [],
            default => [MyList::factory()->create()],
        };
        $this->expectException(InvalidArgumentException::class);
        $repository->$method(...[...$arguments, 1, 20, 'id', 'invalid']);
    }
}
