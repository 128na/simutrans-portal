<?php

namespace Database\Factories\User;

use App\Models\Article;
use App\Models\User\Bookmark;
use App\Models\User\BookmarkItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarItemkFactory extends Factory
{
    protected $model = BookmarkItem::class;

    public function definition()
    {
        return [
            'bookmark_id' => Bookmark::factory()->create()->id,
            'bookmark_itemable_type' => Article::class,
            'bookmark_itemable_id' => Article::factory()->create()->id,
            'memo' => random_int(0, 1) ? null : $this->faker->text,
        ];
    }
}
