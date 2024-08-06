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
    public function testTop(): void
    {
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
    }

    public function testRanking(): void
    {
        $testResponse = $this->get(route('ranking'));

        $testResponse->assertOk();
    }

    public function testPages(): void
    {
        $testResponse = $this->get(route('pages'));

        $testResponse->assertOk();
    }

    public function testAnnounces(): void
    {
        $testResponse = $this->get(route('announces'));

        $testResponse->assertOk();
    }

    public function testCategoryPakNoneAddon(): void
    {
        $pak = Category::factory()->create(['type' => CategoryType::Pak]);
        $testResponse = $this->get(route('category.pak.noneAddon', ['size' => $pak->slug]));

        $testResponse->assertOk();
    }

    public function testCategoryPakNoneAddon存在しないPak(): void
    {
        $testResponse = $this->get(route('category.pak.noneAddon', ['size' => 'missing']));

        $testResponse->assertNotFound();
    }

    public function testCategoryPakAddon(): void
    {
        $pak = Category::factory()->create(['type' => CategoryType::Pak]);
        $addon = Category::factory()->create(['type' => CategoryType::Addon]);
        $testResponse = $this->get(route('category.pak.addon', ['size' => $pak->slug, 'slug' => $addon->slug]));

        $testResponse->assertOk();
    }

    public function testCategoryPakAddon存在しないPak(): void
    {
        $addon = Category::factory()->create(['type' => CategoryType::Addon]);
        $testResponse = $this->get(route('category.pak.addon', ['size' => 'missing', 'slug' => $addon->slug]));

        $testResponse->assertNotFound();
    }

    public function testCategoryPakAddon存在しないAddon(): void
    {
        $pak = Category::factory()->create(['type' => CategoryType::Pak]);
        $testResponse = $this->get(route('category.pak.addon', ['size' => $pak->slug, 'slug' => 'missing']));

        $testResponse->assertNotFound();
    }

    public function testCategory(): void
    {
        $category = Category::factory()->create();
        $testResponse = $this->get(route('category', ['type' => $category->type->value, 'slug' => $category->slug]));

        $testResponse->assertOk();
    }

    public function testCategory存在しないtype(): void
    {
        $category = Category::factory()->create();
        $testResponse = $this->get(route('category', ['type' => 'missing', 'slug' => $category->slug]));

        $testResponse->assertNotFound();
    }

    public function testCategory存在しないslug(): void
    {
        $category = Category::factory()->create();
        $testResponse = $this->get(route('category', ['type' => $category->type->value, 'slug' => 'missing']));

        $testResponse->assertNotFound();
    }

    public function testTag(): void
    {
        $tag = Tag::factory()->create();
        $testResponse = $this->get(route('tag', ['tag' => $tag->id]));

        $testResponse->assertOk();
    }

    public function testTag存在しない(): void
    {
        $testResponse = $this->get(route('tag', ['tag' => -1]));

        $testResponse->assertNotFound();
    }

    public function testUser_id(): void
    {
        $user = User::factory()->create();
        $testResponse = $this->get(route('user', ['userIdOrNickname' => $user->id]));

        $testResponse->assertOk();
    }

    public function testUser_nickname(): void
    {
        $user = User::factory()->create(['nickname' => 'dummy']);
        $testResponse = $this->get(route('user', ['userIdOrNickname' => $user->nickname]));

        $testResponse->assertOk();
    }

    public function testUser存在しない(): void
    {
        $testResponse = $this->get(route('user', ['userIdOrNickname' => -1]));

        $testResponse->assertNotFound();
    }

    public function testTags(): void
    {
        $testResponse = $this->get(route('tags'));

        $testResponse->assertOk();
    }

    public function testShow_id(): void
    {
        $article = Article::factory()->publish()->create();
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id, 'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
    }

    public function testShow_nickname(): void
    {
        $article = Article::factory()->create(['status' => 'publish']);
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user->nickname, 'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
    }

    public function testShow非公開(): void
    {
        $article = Article::factory()->create(['status' => 'private']);
        $testResponse = $this->get(route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]));

        $testResponse->assertNotFound();
    }

    public function testShow_リダイレクトあり(): void
    {
        $article = Article::factory()->publish()->create();

        $from = '/users/' . $article->user_id . '/dummy';
        $to = '/users/' . $article->user_id . $article->slug;
        Redirect::create(['user_id' => $article->user_id, 'from' =>  $from, 'to' =>  $to]);
        $testResponse = $this->get(route('articles.show', ['userIdOrNickname' => $article->user_id, 'articleSlug' => 'dummy']));

        $testResponse->assertRedirect($to);
    }

    public function testSearch(): void
    {
        $testResponse = $this->get(route('search', ['word' => 'foo']));

        $testResponse->assertOk();
    }

    public function testFallbackShow_slug_ニックネーム設定済み(): void
    {
        $article = Article::factory()->publish()->create();

        $testResponse = $this->get('/articles/' . $article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));
    }

    public function testFallbackShow_id_ニックネーム設定済み(): void
    {
        $article = Article::factory()->publish()->create();

        $testResponse = $this->get('/articles/' . $article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));
    }

    public function testFallbackShow_slug_ニックネーム未設定(): void
    {
        $article = Article::factory()->publish()->create();
        $article->user->update(['nickname' => null]);
        $testResponse = $this->get('/articles/' . $article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }

    public function testFallbackShow_id_ニックネーム未設定(): void
    {
        $article = Article::factory()->publish()->create();
        $article->user->update(['nickname' => null]);

        $testResponse = $this->get('/articles/' . $article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }
}
