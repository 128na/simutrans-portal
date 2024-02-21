<?php

declare(strict_types=1);

namespace Database\Seeders\Updates;

use App\Jobs\Article\JobUpdateRelated;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Iterator;

/**
 * DB上のカテゴリ表示順をconfigで定義した順に変更する.
 */
class UpdateCategoryOrder extends Seeder
{
    public function run()
    {
        foreach ($this->getCategories() as $data) {
            $category = Category::slug($data['slug'])->type($data['type'])->first();

            $category->update([
                'order' => $data['order'],
            ]);
        }

        JobUpdateRelated::dispatchSync();
    }

    private function getCategories(): Iterator
    {
        yield from config('category.pak');
        yield from config('category.addon');
        yield from config('category.pak128_position');
        yield from config('category.license');
        yield from config('category.page');
    }
}
