<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Screenshot;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ControllOptionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use Tests\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setup();

        $this->seed(CategorySeeder::class);
        $this->seed(ControllOptionsSeeder::class);
    }

    protected function createFromFile(UploadedFile $uploadedFile, int $userId): Attachment
    {
        return Attachment::factory()->create([
            'user_id' => $userId,
            'path' => $uploadedFile->store('user/'.$userId, 'public'),
            'original_name' => $uploadedFile->getClientOriginalName(),
        ]);
    }

    protected function createAttachment(User $user): Attachment
    {
        $file = UploadedFile::fake()->create('file.zip', 1, 'application/zip');

        return $this->createFromFile($file, $user->id);
    }

    protected function createScreenshot(?User $user = null): Screenshot
    {
        $user ??= User::factory()->create();
        $attachment = $this->createAttachment($user);
        $scrrenshot = Screenshot::factory()->create([
            'user_id' => $user->id,
        ]);
        $scrrenshot->attachments()->save($attachment);

        return $scrrenshot;
    }

    protected function createAddonPost(?User $user = null): Article
    {
        $user ??= User::factory()->create();
        $attachment = $this->createAttachment($user);
        $article = Article::factory()->addonPost($attachment)->create([
            'user_id' => $user->id,
            'status' => ArticleStatus::Publish,
        ]);
        $article->attachments()->save($attachment);

        return $article;
    }

    protected function createAddonIntroduction(?User $user = null): Article
    {
        $user ??= User::factory()->create();

        return Article::factory()->addonIntroduction()->create([
            'user_id' => $user->id,
            'status' => ArticleStatus::Publish,
        ]);
    }

    protected function createPage(?User $user = null): Article
    {
        $user ??= User::factory()->create();

        return Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => ArticlePostType::Page,
            'status' => ArticleStatus::Publish,
            'title' => 'test_page',
            'contents' => [
                'sections' => [['type' => 'text', 'text' => 'test page text']],
            ],
        ]);
    }

    protected function createMarkdown(?User $user = null): Article
    {
        $user ??= User::factory()->create();

        return Article::factory()->markdown()->create([
            'user_id' => $user->id,
            'status' => ArticleStatus::Publish,
        ]);
    }

    protected function createAnnounce(?User $user = null): Article
    {
        $user ??= User::factory()->create();
        $article = Article::factory()->page()->create([
            'user_id' => $user->id,
            'status' => ArticleStatus::Publish,
        ]);
        $category = Category::firstOrCreate(['type' => CategoryType::Page, 'slug' => 'announce']);
        $article->categories()->save($category);

        return $article;
    }

    protected function createMarkdownAnnounce(?User $user = null): Article
    {
        $user ??= User::factory()->create();
        $article = Article::factory()->markdown()->create([
            'user_id' => $user->id,
            'status' => ArticleStatus::Publish,
        ]);
        $category = Category::firstOrCreate(['type' => CategoryType::Page, 'slug' => 'announce']);
        $article->categories()->save($category);

        return $article;
    }

    protected function attachRandomCategory(Article $article, CategoryType $categoryType): Article
    {
        $category = Category::where('type', $categoryType)->inRandomOrder()->first();

        return $this->attachCategory($article, $category);
    }

    protected function attachCategory(Article $article, Category $category): Article
    {
        $article->categories()->save($category);

        return $article;
    }
}
