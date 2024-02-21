<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * お知らせ記事.
 */
class AnnounceSeeder extends Seeder
{
    public function run()
    {
        $admin = $this->addAdminUser();
        $this->addAnounces($admin);
    }

    private function addAdminUser()
    {
        if (is_null(config('admin.email'))) {
            throw new \Exception('admin email was empty!');
        }

        return User::firstOrCreate(
            ['role' => config('role.admin'), 'name' => config('admin.name'), 'email' => config('admin.email')],
            ['password' => bcrypt(config('admin.password')), 'email_verified_at' => now()]
        );
    }

    /**
     * お知らせ記事作成.
     */
    private function addAnounces($user)
    {
        $announce_category = Category::page()->slug('announce')->firstOrFail();

        foreach ($this->getAnnouces() as $data) {
            $data = array_merge([
                'post_type' => config('post_types.page'),
                'status' => config('status.publish'),
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
