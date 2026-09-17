<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Enums\CategoryType;
use App\Models\Category;
use Tests\Feature\TestCase;

class PakControllerTest extends TestCase
{
    public function test_latestページのタイルurlは日本語スラッグでも二重エンコードされない(): void
    {
        $article = $this->createAddonPost();
        $article->update(['title' => '日本語タイル記事', 'slug' => '日本語タイル記事']);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
        $article->categories()->save($category);

        $testResponse = $this->get(route('latest'));

        $testResponse->assertOk();
        $testResponse->assertDontSee('%25', false);
        $testResponse->assertSee($article->showUrl(), false);
    }

    public function test_pak128_japan(): void
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

    public function test_pak_others(): void
    {
        $testResponse = $this->get(route('pak.others'));

        $testResponse->assertOk();
    }
}
