<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\ArticleSearchIndex\UpdateOrCreateAction;
use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class ArticleSearchIndexSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(UpdateOrCreateAction $updateOrCreateAction): void
    {
        Article::query()->withoutGlobalScopes()->chunkById(200, function ($articles) use ($updateOrCreateAction): void {
            foreach ($articles as $article) {
                $updateOrCreateAction($article->id);
            }
        });
    }
}
