<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Web;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Redirect;
use App\Models\Tag;
use App\Models\User;
use Tests\Feature\TestCase;

final class FrontControllerTest extends TestCase
{
    public function test_top(): void
    {
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
    }

    public function test_ranking(): void
    {
        $testResponse = $this->get(route('ranking'));

        $testResponse->assertOk();
    }

    public function test_pages(): void
    {
        $testResponse = $this->get(route('pages'));

        $testResponse->assertOk();
    }

    public function test_announces(): void
    {
        $testResponse = $this->get(route('announces'));

        $testResponse->assertOk();
    }

    public function test_category_pak_none_addon(): void
    {
        $pak = Category::factory()->create(['type' => CategoryType::Pak]);
        $testResponse = $this->get(route('category.pak.noneAddon', ['size' => $pak->slug]));

        $testResponse->assertOk();
    }

    public function test_category_pak_none_addon存在しない_pak(): void
    {
        $testResponse = $this->get(route('category.pak.noneAddon', ['size' => 'missing']));

        $testResponse->assertNotFound();
    }

    public function test_category_pak_addon(): void
    {
        $pak = Category::factory()->create(['type' => CategoryType::Pak]);
        $addon = Category::factory()->create(['type' => CategoryType::Addon]);
        $testResponse = $this->get(route('category.pak.addon', ['size' => $pak->slug, 'slug' => $addon->slug]));

        $testResponse->assertOk();
    }

    public function test_category_pak_addon存在しない_pak(): void
    {
        $addon = Category::factory()->create(['type' => CategoryType::Addon]);
        $testResponse = $this->get(route('category.pak.addon', ['size' => 'missing', 'slug' => $addon->slug]));

        $testResponse->assertNotFound();
    }

    public function test_category_pak_addon存在しない_addon(): void
    {
        $pak = Category::factory()->create(['type' => CategoryType::Pak]);
        $testResponse = $this->get(route('category.pak.addon', ['size' => $pak->slug, 'slug' => 'missing']));

        $testResponse->assertNotFound();
    }

    public function test_category(): void
    {
        $category = Category::factory()->create();
        $testResponse = $this->get(route('category', ['type' => $category->type->value, 'slug' => $category->slug]));

        $testResponse->assertOk();
    }

    public function test_category存在しないtype(): void
    {
        $category = Category::factory()->create();
        $testResponse = $this->get(route('category', ['type' => 'missing', 'slug' => $category->slug]));

        $testResponse->assertNotFound();
    }

    public function test_category存在しないslug(): void
    {
        $category = Category::factory()->create();
        $testResponse = $this->get(route('category', ['type' => $category->type->value, 'slug' => 'missing']));

        $testResponse->assertNotFound();
    }

    public function test_tag(): void
    {
        $tag = Tag::factory()->create();
        $testResponse = $this->get(route('tag', ['tag' => $tag->id]));

        $testResponse->assertOk();
    }

    public function test_tag存在しない(): void
    {
        $testResponse = $this->get(route('tag', ['tag' => -1]));

        $testResponse->assertNotFound();
    }

    public function test_user_id(): void
    {
        $user = User::factory()->create();
        $testResponse = $this->get(route('user', ['userIdOrNickname' => $user->id]));

        $testResponse->assertOk();
    }

    public function test_user_nickname(): void
    {
        $user = User::factory()->create(['nickname' => 'dummy']);
        $testResponse = $this->get(route('user', ['userIdOrNickname' => $user->nickname]));

        $testResponse->assertOk();
    }

    public function test_user存在しない(): void
    {
        $testResponse = $this->get(route('user', ['userIdOrNickname' => -1]));

        $testResponse->assertNotFound();
    }

    public function test_tags(): void
    {
        $testResponse = $this->get(route('tags'));

        $testResponse->assertOk();
    }

    public function test_show_id(): void
    {
        $article = Article::factory()->publish()->create();
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id, 'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
    }

    public function test_show_nickname(): void
    {
        $article = Article::factory()->create(['status' => 'publish']);
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user->nickname, 'articleSlug' => $article->slug,
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

    public function test_search(): void
    {
        $testResponse = $this->get(route('search', ['word' => 'foo']));

        $testResponse->assertOk();
    }

    public function test_fallback_show_slug_ニックネーム設定済み(): void
    {
        $article = Article::factory()->publish()->create();

        $testResponse = $this->get('/articles/'.$article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));
    }

    public function test_fallback_show_id_ニックネーム設定済み(): void
    {
        $article = Article::factory()->publish()->create();

        $testResponse = $this->get('/articles/'.$article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));
    }

    public function test_fallback_show_slug_ニックネーム未設定(): void
    {
        $article = Article::factory()->publish()->create();
        $article->user->update(['nickname' => null]);
        $testResponse = $this->get('/articles/'.$article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }

    public function test_fallback_show_id_ニックネーム未設定(): void
    {
        $article = Article::factory()->publish()->create();
        $article->user->update(['nickname' => null]);

        $testResponse = $this->get('/articles/'.$article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }
}
