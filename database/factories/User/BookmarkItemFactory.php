<?php

namespace Database\Factories\User;

use App\Models\Category;
use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarkItemFactory extends Factory
{
    protected $model = BookmarkItem::class;

    public function definition()
    {
        $type = $this->faker->randomElement(BookmarkItem::BOOKMARK_ITEMABLE_TYPES);
        if ($type === Category::class) {
            $id = $type::inRandomOrder()->first()->id;
        } else {
            $id = $type::factory()->create()->id;
        }

        return [
            'bookmark_id' => Bookmark::factory()->create()->id,
            'bookmark_itemable_type' => $type,
            'bookmark_itemable_id' => $id,
            'memo' => random_int(0, 1) ? null : $this->faker->text(),
            'order' => random_int(0, 1) ? 0 : random_int(0, 1000),
        ];
    }
}
