<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Enums\CategoryType;
use App\Models\Category;
use Tests\Feature\TestCase;

class TopControllerTest extends TestCase
{
    public function test_top(): void
    {
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
    }

    public function test_top_shows_the_eight_latest_unique_cards_and_both_dates_for_updates(): void
    {
        $pak64 = Category::where('type', CategoryType::Pak)->where('slug', '64')->sole();
        $pak128 = Category::where('type', CategoryType::Pak)->where('slug', '128')->sole();
        $recent = now()->subDay();
        $ids = [];
        for ($i = 0; $i < 9; $i++) {
            $article = $this->createAddonIntroduction();
            $article->update([
                'modified_at' => $recent->subDays($i),
                'published_at' => $recent->subDays($i + 1),
            ]);
            $article->categories()->attach(match ($i) {
                0 => [$pak64->id, $pak128->id],
                2 => [$pak128->id],
                default => [$pak64->id],
            });
            $ids[] = $article->id;
        }
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $this->get(route('index'))
            ->assertOk()
            ->assertViewHas('latestCards', fn ($cards): bool => $cards->pluck('id')->values()->all() === array_slice($ids, 0, 8))
            ->assertSee('新着アドオンをもっと見る')
            ->assertSee('更新 <time datetime="'.$recent->toIso8601String().'">'.$recent->toDateString().'</time>', false)
            ->assertSee('公開 <time datetime="'.$recent->subDay()->toIso8601String().'">'.$recent->subDay()->toDateString().'</time>', false);

        $this->get(route('latest'))->assertOk()
            ->assertSee('href="#pak64"', false)
            ->assertSee('id="pak64"', false)
            ->assertSee(route('pak.others'));
    }

    public function test_cards_show_a_new_or_updated_badge_and_same_day_edits_stay_new(): void
    {
        $pak64 = Category::where('type', CategoryType::Pak)->where('slug', '64')->sole();
        $published = now()->subDays(3)->setTime(10, 0);
        $updated = $this->createAddonIntroduction();
        $updated->update(['title' => '更新された記事', 'published_at' => $published, 'modified_at' => $published->addDay()]);
        $sameDay = $this->createAddonIntroduction();
        $sameDay->update(['title' => '公開直後に直した記事', 'published_at' => $published, 'modified_at' => $published->addSeconds(5)]);
        $updated->categories()->attach($pak64);
        $sameDay->categories()->attach($pak64);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $html = (string) $this->get(route('index'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/aria-hidden="true"[^>]*>更新<\/span>(?:(?!<\/a>).)*更新された記事/s', $html);
        $this->assertMatchesRegularExpression('/aria-hidden="true"[^>]*>新規<\/span>(?:(?!<\/a>).)*公開直後に直した記事/s', $html);
    }

    public function test_お知らせ一覧のリンクは日本語スラッグでも二重エンコードされない(): void
    {
        $article = $this->createAnnounce();
        $article->update([
            'title' => '日本語のお知らせ記事',
            'slug' => '日本語のお知らせ記事',
        ]);

        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
        $html = (string) $testResponse->getContent();
        $this->assertStringNotContainsString('%25', $html);
        $this->assertStringContainsString(e($article->fresh()?->showUrl()), $html);
    }
}
