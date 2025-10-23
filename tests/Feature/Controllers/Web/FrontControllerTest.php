<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use App\Models\Article;
use App\Models\Redirect;
use Tests\Feature\TestCase;

final class FrontControllerTest extends TestCase
{
    public function test_top(): void
    {
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
    }

    public function test_pak128Japan(): void
    {
        $testResponse = $this->get(route('pak.128japan'));

        $testResponse->assertOk();
    }

    public function test_pak128(): void
    {
        $testResponse = $this->get(route('pak.128'));

        $testResponse->assertOk();
    }

    public function test_pak64(): void
    {
        $testResponse = $this->get(route('pak.64'));

        $testResponse->assertOk();
    }

    public function test_pakOthers(): void
    {
        $testResponse = $this->get(route('pak.others'));

        $testResponse->assertOk();
    }

    public function test_announces(): void
    {
        $testResponse = $this->get(route('announces'));

        $testResponse->assertOk();
    }

    public function test_show_id(): void
    {
        $article = Article::factory()->publish()->create();
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
    }

    public function test_show_nickname(): void
    {
        $article = Article::factory()->create(['status' => 'publish']);
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

        $from = '/users/' . $article->user_id . '/dummy';
        $to = '/users/' . $article->user_id . $article->slug;
        Redirect::create(['user_id' => $article->user_id, 'from' => $from, 'to' => $to]);
        $testResponse = $this->get(route('articles.show', ['userIdOrNickname' => $article->user_id, 'articleSlug' => 'dummy']));

        $testResponse->assertRedirect($to);
    }

    public function test_search(): void
    {
        $testResponse = $this->get(route('search', ['word' => 'foo']));

        $testResponse->assertOk();
    }

    public function test_fallback_show_slug_ニックネーム設定済み(): void
    {
        $article = Article::factory()->publish()->create();

        $testResponse = $this->get('/articles/' . $article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));
    }

    public function test_fallback_show_id_ニックネーム設定済み(): void
    {
        $article = Article::factory()->publish()->create();

        $testResponse = $this->get('/articles/' . $article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));
    }

    public function test_fallback_show_slug_ニックネーム未設定(): void
    {
        $article = Article::factory()->publish()->create();
        $article->user->update(['nickname' => null]);

        $testResponse = $this->get('/articles/' . $article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }

    public function test_fallback_show_id_ニックネーム未設定(): void
    {
        $article = Article::factory()->publish()->create();
        $article->user->update(['nickname' => null]);

        $testResponse = $this->get('/articles/' . $article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }
}
