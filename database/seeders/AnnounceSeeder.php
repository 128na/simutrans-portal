<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\UserRole;
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
        $admin = $this->addAdminUser();
        $this->addAnounces($admin);
    }

    private function addAdminUser()
    {
        if (is_null(env('ADMIN_EMAIL'))) {
            return;
        }

        return User::firstOrCreate(
            ['role' => UserRole::Admin, 'name' => env('ADMIN_NAME'), 'email' => env('ADMIN_EMAIL')],
            ['password' => bcrypt(env('ADMIN_PASSWORD')), 'email_verified_at' => now()]
        );
    }

    /**
     * お知らせ記事作成.
     */
    private function addAnounces($user): void
    {
        $announce_category = Category::page()->slug('announce')->firstOrFail();

        foreach ($this->getAnnouces() as $data) {
            $data = array_merge([
                'post_type' => ArticlePostType::Page,
                'status' => ArticleStatus::Publish,
            ], $data);

            $article = $user->articles()->firstOrCreate(['slug' => $data['slug']], $data);
            $article->categories()->sync($announce_category->id);
        }
    }

    private function getAnnouces(): array
    {
        return require resource_path('articles\announces.php');
    }
}
