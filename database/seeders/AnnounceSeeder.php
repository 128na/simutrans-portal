<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * お知らせ記事.
 */
class AnnounceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::admin()->firstOrFail();
        $announceCategory = Category::page()->slug('announce')->firstOrFail();

        foreach ($this->getAnnouces() as $data) {
            $data = array_merge([
                'post_type' => ArticlePostType::Page,
                'status' => ArticleStatus::Publish,
            ], $data);

            $article = $admin->articles()->firstOrCreate(['slug' => $data['slug']], $data);
            $article->categories()->sync($announceCategory->id);
        }
    }

    private function getAnnouces(): array
    {
        return require resource_path('articles\announces.php');
    }
}
