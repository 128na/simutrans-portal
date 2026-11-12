<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Front;

use App\Services\Front\ArticleListFilter;
use Tests\Unit\TestCase;

/**
 * 種類の切り替えに並べる選択肢の出し分け（0 件を出さない・少ない種類を畳む）
 */
class ArticleListFilterTest extends TestCase
{
    /**
     * 件数はどれも違う値にして、件数順と定義順の取り違えが分かるようにする
     *
     * @var array<string, int>
     */
    private const array COUNTS = [
        'trains' => 430,
        'rail-tools' => 73,
        'road-tools' => 59,
        'ships' => 7,
        'aircrafts' => 5,
        'road-vehicles' => 53,
        'airport-tools' => 2,
        'industrial-tools' => 18,
        'buildings' => 33,
        'maglev-vehicles' => 0,
        'others' => 31,
        'none' => 0,
    ];

    public function test_件数が多い上位だけを定義順で出し残りを畳む(): void
    {
        $groups = (new ArticleListFilter)->addonChipGroups(self::COUNTS, 3);

        $this->assertSame(['trains', 'rail-tools', 'road-tools'], array_keys($groups['visible']));
        $this->assertSame('鉄道車両', $groups['visible']['trains']);
        $this->assertSame(
            ['ships', 'aircrafts', 'road-vehicles', 'airport-tools', 'industrial-tools', 'buildings', 'others'],
            array_keys($groups['folded']),
        );
    }

    public function test_常に出す数は件数の順位で決め並びは定義順にそろえる(): void
    {
        $groups = (new ArticleListFilter)->addonChipGroups(self::COUNTS, 5);

        $this->assertSame(['trains', 'rail-tools', 'road-tools', 'road-vehicles', 'buildings'], array_keys($groups['visible']));
    }

    public function test_0件と件数の無い種類は畳んだ側にも出さない(): void
    {
        $groups = (new ArticleListFilter)->addonChipGroups(self::COUNTS, 3);
        $listed = [...array_keys($groups['visible']), ...array_keys($groups['folded'])];

        $this->assertNotContains('maglev-vehicles', $listed);
        $this->assertNotContains('none', $listed);
        $this->assertNotContains('scripts', $listed, '件数に無い種類は 0 件として扱う');
        $this->assertCount(10, $listed);
    }

    public function test_選択中の種類は件数が少なくても常に出す(): void
    {
        $groups = (new ArticleListFilter(addonSlug: 'airport-tools'))->addonChipGroups(self::COUNTS, 3);

        $this->assertSame(['trains', 'rail-tools', 'airport-tools'], array_keys($groups['visible']), '上位の最後と入れ替えて数を変えない');
        $this->assertArrayNotHasKey('airport-tools', $groups['folded']);
        $this->assertArrayHasKey('road-tools', $groups['folded']);
    }

    public function test_選択中の種類は0件でも常に出す(): void
    {
        $groups = (new ArticleListFilter(addonSlug: 'maglev-vehicles'))->addonChipGroups(self::COUNTS, 3);

        $this->assertSame(['trains', 'rail-tools', 'maglev-vehicles'], array_keys($groups['visible']));
        $this->assertArrayNotHasKey('maglev-vehicles', $groups['folded']);
    }

    public function test_選択中の種類が上位に入っていれば並びも数も変えない(): void
    {
        $groups = (new ArticleListFilter(addonSlug: 'rail-tools'))->addonChipGroups(self::COUNTS, 3);

        $this->assertSame(['trains', 'rail-tools', 'road-tools'], array_keys($groups['visible']));
    }

    public function test_主要な種類だけを定義順で出し残りはすべて畳む(): void
    {
        // 優先順と定義順をわざと違えて、並びが定義順にそろうことを確かめる
        $groups = (new ArticleListFilter)->addonChipGroupsByPrimary(['buildings', 'trains', 'ships']);

        $this->assertSame(['trains', 'ships', 'buildings'], array_keys($groups['visible']));
        $this->assertSame('鉄道車両', $groups['visible']['trains']);
        $this->assertArrayNotHasKey('trains', $groups['folded']);
        $this->assertSame(
            array_keys(array_diff_key(ArticleListFilter::addonOptions(), $groups['visible'])),
            array_keys($groups['folded']),
            '件数が分からないので、どの種類も消さずに定義順で畳む',
        );
        $this->assertArrayHasKey('none', $groups['folded']);
    }

    public function test_主要な種類に無い選択中の種類は優先順の最後と入れ替えて出す(): void
    {
        $groups = (new ArticleListFilter(addonSlug: 'airport-tools'))->addonChipGroupsByPrimary(['buildings', 'trains', 'ships']);

        $this->assertSame(['trains', 'airport-tools', 'buildings'], array_keys($groups['visible']), '数を変えず、優先順の最後（船舶）を畳む');
        $this->assertArrayHasKey('ships', $groups['folded']);
        $this->assertArrayNotHasKey('airport-tools', $groups['folded']);
    }

    public function test_選択肢に無い種類が選択中でも主要な種類を減らさない(): void
    {
        $groups = (new ArticleListFilter(addonSlug: 'unknown-addon'))->addonChipGroupsByPrimary(['buildings', 'trains', 'ships']);

        $this->assertSame(['trains', 'ships', 'buildings'], array_keys($groups['visible']));
    }

    public function test_主要な種類に入っている選択中の種類は並びも数も変えない(): void
    {
        $groups = (new ArticleListFilter(addonSlug: 'ships'))->addonChipGroupsByPrimary(['buildings', 'trains', 'ships']);

        $this->assertSame(['trains', 'ships', 'buildings'], array_keys($groups['visible']));
    }

    public function test_検索結果の主要な種類はすべて選択肢にあり常に出す数とそろう(): void
    {
        $this->assertCount(ArticleListFilter::ADDON_VISIBLE_LIMIT, ArticleListFilter::SEARCH_PRIMARY_ADDONS);
        $this->assertSame([], array_diff(ArticleListFilter::SEARCH_PRIMARY_ADDONS, array_keys(ArticleListFilter::addonOptions())));
    }

    public function test_件数が無い一覧ではすべての種類を出し畳まない(): void
    {
        $groups = (new ArticleListFilter)->addonChipGroups(null, 3);

        $this->assertSame(ArticleListFilter::addonOptions(), $groups['visible']);
        $this->assertSame([], $groups['folded']);
    }
}
