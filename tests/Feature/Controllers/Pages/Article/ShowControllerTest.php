<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Models\Article;
use App\Models\Redirect;
use Tests\Feature\TestCase;

final class ShowControllerTest extends TestCase
{
    public function test_show(): void
    {
        $article = Article::factory()->publish()->create();
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]));
        $testResponse->assertOk();

        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user->nickname,
            'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
    }

    public function test_show非公開(): void
    {
        $article = Article::factory()->create(['status' => 'private']);
        $testResponse = $this->get(route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]));

        $testResponse->assertNotFound();
    }

    public function test_show_リダイレクトあり(): void
    {
        $article = Article::factory()->publish()->create();

        $from = '/users/'.$article->user_id.'/dummy';
        $to = '/users/'.$article->user_id.$article->slug;
        Redirect::create(['user_id' => $article->user_id, 'from' => $from, 'to' => $to]);
        $testResponse = $this->get(route('articles.show', ['userIdOrNickname' => $article->user_id, 'articleSlug' => 'dummy']));

        $testResponse->assertRedirect($to);
    }

    public function test_fallback_show_ニックネーム設定済み(): void
    {
        $article = Article::factory()->publish()->create();

        $testResponse = $this->get('/articles/'.$article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));

        $testResponse = $this->get('/articles/'.$article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));
    }

    public function test_fallback_show_ニックネーム未設定(): void
    {
        $article = Article::factory()->publish()->create();
        $article->user->update(['nickname' => null]);

        $testResponse = $this->get('/articles/'.$article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));

        $testResponse = $this->get('/articles/'.$article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }

    public function test_show_json_response(): void
    {
        $article = Article::factory()->publish()->create();

        // Test with .json extension
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug.'.json',
        ]));

        $testResponse->assertOk();
        $testResponse->assertJson([
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
        ]);

        // Test with Accept: application/json header
        $testResponse = $this->getJson(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
        $testResponse->assertJson([
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
        ]);
    }
}
