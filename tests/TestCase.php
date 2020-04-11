<?php

namespace Tests;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Contents\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static function createAddonPost($user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $file = UploadedFile::fake()->create('document.zip', 1);
        $attachment = Attachment::createFromFile($file, $user->id);
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'addon-post',
            'title' => 'test_addon-post',
            'status' => 'publish',
            'contents' => Content::createFromType('addon-post', [
                'description' => 'test addon-post text',
                'author' => 'test author',
                'file' => $attachment->id,
            ]),
        ]);
        $article->attachments()->save($attachment);
        return $article;
    }
    protected static function createAddonIntroduction($user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'addon-introduction',
            'title' => 'test_addon-introduction',
            'status' => 'publish',
            'contents' => Content::createFromType('addon-introduction', [
                'description' => 'test addon-introduction text',
                'author' => 'test author',
                'link' => 'http://example.com',
            ]),
        ]);
        return $article;
    }
    protected static function createPage($user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'page',
            'title' => 'test_page',
            'status' => 'publish',
            'contents' => Content::createFromType('page', [
                'sections' => [['type' => 'text', 'text' => 'test page text']],
            ]),
        ]);
        return $article;
    }
    protected static function createMarkdown($user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'markdown',
            'title' => 'test_markdown',
            'status' => 'publish',
            'contents' => Content::createFromType('markdown', [
                'markdown' => '# test markdown text',
            ]),
        ]);
        return $article;
    }
    protected static function createAnnounce($user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'page',
            'title' => 'test_announce',
            'status' => 'publish',
            'contents' => Content::createFromType('page', [
                'sections' => [['type' => 'text', 'text' => 'test announce text']],
            ]),
        ]);
        $announce_category = Category::page()->slug('announce')->firstOrFail();
        $article->categories()->save($announce_category);
        return $article;
    }
    protected static function createMarkdownAnnounce($user = null)
    {
        $user = $user ?: factory(User::class)->create();
        $article = factory(Article::class)->create([
            'user_id' => $user->id,
            'post_type' => 'markdown',
            'title' => 'test_markdown',
            'status' => 'publish',
            'contents' => Content::createFromType('markdown', [
                'markdown' => '# test markdown text',
            ]),
        ]);
        $announce_category = Category::page()->slug('announce')->firstOrFail();
        $article->categories()->save($announce_category);
        return $article;
    }
}
