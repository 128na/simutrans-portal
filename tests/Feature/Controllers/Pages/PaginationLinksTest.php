<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

/**
 * 公開ページの一覧のページ送り（#682）。
 * 番号を出しすぎると狭い幅でページ全体が横に広がるので、現在のページの前後 1 ページまでに絞り、
 * 件数の文と番号の列は幅が足りなければ 2 行に分ける。
 */
class PaginationLinksTest extends TestCase
{
    /** 1 ページの件数（一覧の既定） */
    private const PER_PAGE = 24;

    /** 10 ページ。前後 3 ページ（Laravel の既定）なら全部の番号を出し、前後 1 ページなら途中を省く */
    private const PAGES = 10;

    /**
     * @return array<string, array{string}>
     */
    public static function listProvider(): array
    {
        return [
            'pak別' => ['pak'],
            'pak×種類' => ['pakAddon'],
            'タグ' => ['tag'],
            'ユーザー' => ['user'],
            '検索結果' => ['search'],
            '一般記事' => ['pages'],
            'お知らせ' => ['announces'],
        ];
    }

    #[DataProvider('listProvider')]
    public function test_ページ送りは前後1ページまでの番号に絞り狭い幅では折り返す(string $list): void
    {
        $html = $this->get($this->prepare($list))->assertOk()->getContent();
        $this->assertIsString($html);

        $this->assertSame(1, preg_match('/<nav\s+role="navigation"(?:(?!<\/nav>).)*<\/nav>/s', $html, $nav));
        $pagination = html_entity_decode($nav[0]);

        // 1 ページ目は 1〜6 と最後の 2 ページだけを出し、間は「...」で省く
        foreach ([2, 6, 9, 10] as $page) {
            $this->assertMatchesRegularExpression('/href="[^"]*[?&]page='.$page.'"/', $pagination, "{$page} ページ目への番号を出す");
        }
        foreach ([7, 8] as $page) {
            $this->assertDoesNotMatchRegularExpression('/href="[^"]*[?&]page='.$page.'"/', $pagination, "{$page} ページ目の番号は省く");
        }
        $this->assertMatchesRegularExpression('/<span class="v2-pagination-item">\.\.\.<\/span>/', $pagination);

        // 件数の文と番号の列の行は、幅が足りなければ折り返す。番号の列がそれでも収まらなければ列の中だけを横にスクロールさせる
        $this->assertMatchesRegularExpression('/<div\s+class="(?=(?:[^"]*\s)?sm:flex[\s"])(?=(?:[^"]*\s)?sm:flex-wrap[\s"])[^"]*"\s*>/', $pagination);
        $this->assertMatchesRegularExpression('/<div class="(?=(?:[^"]*\s)?max-w-full[\s"])(?=(?:[^"]*\s)?overflow-x-auto[\s"])[^"]*">\s*<span class="v2-pagination">/', $pagination);
    }

    /**
     * 一覧に 10 ページ分の記事を用意し、1 ページ目の URL を返す
     */
    private function prepare(string $list): string
    {
        // ユーザー・タグの ID がほかの ID と重ならないよう、無関係なデータを先に作る
        User::factory()->count(2)->create();
        Tag::factory()->count(3)->create();

        $user = User::factory()->create();
        $count = self::PER_PAGE * (self::PAGES - 1) + 1;

        if ($list === 'pages' || $list === 'announces') {
            $slug = $list === 'pages' ? 'common' : 'announce';
            $category = Category::firstOrCreate(['type' => CategoryType::Page, 'slug' => $slug]);
            $articleIds = Article::factory()->page()->publish()->count($count)->create(['user_id' => $user->id])->modelKeys();
            $category->articles()->attach($articleIds);

            return route($list);
        }

        $tag = Tag::factory()->create();
        $pak = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
        $trains = Category::where('type', CategoryType::Addon)->where('slug', 'trains')->firstOrFail();
        $articleIds = Article::factory()->addonIntroduction()->publish()->count($count)->create(['user_id' => $user->id])->modelKeys();
        $pak->articles()->attach($articleIds);
        $trains->articles()->attach($articleIds);
        $tag->articles()->attach($articleIds);

        return match ($list) {
            'pak' => route('pak.128japan'),
            'pakAddon' => route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains']),
            'tag' => route('tags.show', ['tag' => $tag]),
            'user' => route('users.show', ['userIdOrNickname' => $user->id]),
            'search' => route('search', ['categoryIds' => [$pak->id]]),
        };
    }
}
