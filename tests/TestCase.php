<?php

namespace Tests;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * 一般ユーザー
     */
    protected User $user;

    /**
     * 一般ユーザーの公開記事.
     */
    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
        $this->user = User::factory()->create();
        $this->article = Article::factory()->create(['user_id' => $this->user->id, 'status' => 'publish']);
    }

    protected function createAddonPost($user = null)
    {
        $user = $user ?? $this->user;
        $file = new UploadedFile(Storage::path('testing/test.zip'), 'test.zip');
        $attachment = Attachment::createFromFile($file, $user->id);
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'addon-post',
            'title' => 'test_addon-post',
            'status' => 'publish',
            'contents' => [
                'description' => 'test addon-post text',
                'author' => 'test author',
                'file' => $attachment->id,
            ],
        ]);
        $article->attachments()->save($attachment);

        return $article;
    }

    protected function createAddonIntroduction($user = null)
    {
        $user = $user ?? $this->user;
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'addon-introduction',
            'title' => 'test_addon-introduction',
            'status' => 'publish',
            'contents' => [
                'description' => 'test addon-introduction text',
                'author' => 'test author',
                'link' => 'http://example.com',
            ],
        ]);

        return $article;
    }

    protected function createPage($user = null)
    {
        $user = $user ?? $this->user;
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'page',
            'title' => 'test_page',
            'status' => 'publish',
            'contents' => [
                'sections' => [['type' => 'text', 'text' => 'test page text']],
            ],
        ]);

        return $article;
    }

    protected function createMarkdown($user = null)
    {
        $user = $user ?? $this->user;
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'markdown',
            'title' => 'test_markdown',
            'status' => 'publish',
            'contents' => [
                'markdown' => '# test markdown text',
            ],
        ]);

        return $article;
    }

    protected function createAnnounce($user = null)
    {
        $user = $user ?? $this->user;
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'page',
            'title' => 'test_announce',
            'status' => 'publish',
            'contents' => [
                'sections' => [['type' => 'text', 'text' => 'test announce text']],
            ],
        ]);
        $announce_category = Category::page()->slug('announce')->firstOrFail();
        $article->categories()->save($announce_category);

        return $article;
    }

    protected function createMarkdownAnnounce($user = null)
    {
        $user = $user ?? $this->user;
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'post_type' => 'markdown',
            'title' => 'test_markdown',
            'status' => 'publish',
            'contents' => [
                'markdown' => '# test markdown text',
            ],
        ]);
        $announce_category = Category::page()->slug('announce')->firstOrFail();
        $article->categories()->save($announce_category);

        return $article;
    }
}
