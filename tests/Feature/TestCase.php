<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ControllOptionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\CreatesApplication;
use Tests\TestCase as TestsTestCase;

abstract class TestCase extends TestsTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->dirtyDatabaseCheck();

        $this->seed(CategorySeeder::class);
        $this->seed(ControllOptionsSeeder::class);
    }

    #[\Override]
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @param  class-string<\Illuminate\Foundation\Http\FormRequest>  $requestClass
     * @param  array<mixed>  $data
     */
    protected function makeValidator(string $requestClass, array $data): \Illuminate\Contracts\Validation\Validator
    {
        $rules = (new $requestClass($data))->rules();

        return Validator::make($data, $rules);
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

    protected function createImageAttachment(User $user): Attachment
    {
        $file = UploadedFile::fake()->image('test.jpg', 1);

        return $this->createFromFile($file, $user->id);
    }

    protected function createAddonPost(?User $user = null): Article
    {
        $user ??= User::factory()->create();
        $attachment = $this->createAttachment($user);
        $article = Article::factory()->addonPost($attachment)->publish()->create(['user_id' => $user->id]);
        $article->attachments()->save($attachment);

        return $article;
    }

    protected function createAddonIntroduction(?User $user = null): Article
    {
        $user ??= User::factory()->create();

        return Article::factory()->addonIntroduction()->publish()->create(['user_id' => $user->id]);
    }

    protected function createPage(?User $user = null): Article
    {
        $user ??= User::factory()->create();
        $article = Article::factory()->page()->publish()->create(['user_id' => $user->id]);

        // Attach a default Page category (common)
        $category = Category::where('type', CategoryType::Page)->where('slug', 'common')->firstOrFail();
        $article->categories()->save($category);

        return $article;
    }

    protected function createMarkdown(?User $user = null): Article
    {
        $user ??= User::factory()->create();
        $article = Article::factory()->markdown()->publish()->create(['user_id' => $user->id]);

        // Attach a default Page category (common)
        $category = Category::where('type', CategoryType::Page)->where('slug', 'common')->firstOrFail();
        $article->categories()->save($category);

        return $article;
    }

    protected function createAnnounce(?User $user = null): Article
    {
        $user ??= User::factory()->create();
        $article = Article::factory()->page()->publish()->create(['user_id' => $user->id]);
        $category = Category::firstOrCreate(['type' => CategoryType::Page, 'slug' => 'announce']);
        $article->categories()->save($category);

        return $article;
    }

    protected function createMarkdownAnnounce(?User $user = null): Article
    {
        $user ??= User::factory()->create();
        $article = Article::factory()->markdown()->publish()->create(['user_id' => $user->id]);
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

    private function dirtyDatabaseCheck()
    {
        $classes = [Article::class, User::class];
        foreach ($classes as $class) {
            $record = $class::withoutGlobalScopes()->count();
            if ($record !== 0) {
                dump("Database is dirty before test run. Model: {$class}, Record count: {$record}");
                // 明示的にデータベースをクリーンアップ
                try {
                    \Illuminate\Support\Facades\DB::rollBack();
                } catch (\Throwable) {
                    // ロールバックが失敗してもテストは続行
                }
            }
        }
    }
}
