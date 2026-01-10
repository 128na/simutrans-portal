<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Article;
use App\Models\MyList;
use App\Models\MyListItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MyListItem>
 */
class MyListItemFactory extends Factory
{
    protected $model = MyListItem::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'list_id' => MyList::factory(),
            'article_id' => Article::factory()->publish(),
            'note' => fake()->boolean(50) ? fake()->realText(30) : null,
            'position' => fake()->numberBetween(1, 1000),
        ];
    }
}
