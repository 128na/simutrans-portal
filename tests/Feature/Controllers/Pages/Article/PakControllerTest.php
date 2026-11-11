<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

class PakControllerTest extends TestCase
{
    public function test_latestページのタイルurlは日本語スラッグでも二重エンコードされない(): void
    {
        $article = $this->createAddonPost();
        $article->update(['title' => '日本語タイル記事', 'slug' => '日本語タイル記事']);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
        $article->categories()->save($category);

        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $testResponse = $this->get(route('latest'));

        $testResponse->assertOk();
        $testResponse->assertDontSee('%25', false);
        $this->assertSame(
            [$article->showUrl()],
            array_column($this->embeddedLatestCards((string) $testResponse->getContent(), 'pak128-japan'), 'url'),
        );
    }

    public function test_latest_limits_cards_without_counting_and_pak_pages_still_paginate(): void
    {
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
        $articles = [];
        $now = now();
        for ($i = 0; $i < 25; $i++) {
            $article = $this->createAddonIntroduction();
            $article->update(['modified_at' => $now->subMinutes($i)]);
            $article->categories()->attach($category);
            $articles[] = $article;
        }

        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
            $generationQueries = DB::getQueryLog();
            DB::flushQueryLog();
            $response = $this->get(route('latest', ['page' => 2]));
            $renderQueries = DB::getQueryLog();
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        $response->assertOk();
        $response->assertViewHas('pak128Japan', fn (array $cards): bool => array_column($cards, 'id') === array_map(fn ($article): int => $article->id, array_slice($articles, 0, 12)));
        $this->assertSame([], array_values(array_filter($generationQueries, fn (array $query): bool => preg_match('/^select count\(|\b(?:article_tag|profiles)\b/i', $query['query']) === 1)));

        $this->assertSame([], $renderQueries);

        $this->get(route('pak.128japan', ['page' => 2]))
            ->assertOk()
            ->assertViewHas('articles', fn ($articlesResource): bool => $articlesResource->resource->currentPage() === 2
                && $articlesResource->resource->total() === 25
                && $articlesResource->resource->first()?->id === $articles[24]->id);
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
