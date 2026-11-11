<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Front\PublicArticleSnapshot;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * トップ。新着・お知らせ・pak × 種類の件数は保存済みの表示データだけを読み、DB を使わない
 */
class TopController extends Controller
{
    /**
     * pak ごとの一覧ページのルート名
     */
    private const array PAK_ROUTES = [
        '128-japan' => 'pak.128japan',
        '128' => 'pak.128',
        '64' => 'pak.64',
    ];

    /**
     * 件数が無いとき（旧形式・欠落・失効）に「種類から探す」へ出す種類
     */
    private const array FALLBACK_ADDONS = ['trains', 'rail-tools', 'road-tools', 'road-vehicles', 'buildings'];

    private const int ADDONS_PER_PAK = 5;

    private const int LATEST_LIMIT = 8;

    public function __construct(
        private readonly PublicArticleSnapshot $snapshot,
    ) {}

    public function top(Request $request): View
    {
        $articles = $this->snapshot->read();
        $pak = $request->query('pak');
        $selectedPak = is_string($pak) && in_array($pak, PublicArticleSnapshot::PAKS, true) ? $pak : null;

        /** @var list<array<string, mixed>> $pakCards */
        $pakCards = $selectedPak === null ? [] : $articles['latest'][$selectedPak] ?? [];
        $latestCards = $selectedPak === null
            ? collect($articles['latest'])
                ->flatten(1)
                ->unique('id')
                ->sortByDesc(fn (array $card): string => $card['modified_at'] ?? $card['published_at'] ?? '')
                ->take(self::LATEST_LIMIT)
            : collect($pakCards)->take(self::LATEST_LIMIT);

        return view('pages.top.index', [
            'announces' => $articles['announces'],
            'latestCards' => $latestCards,
            'selectedPak' => $selectedPak,
            'pakTabs' => array_map(fn (string $slug): array => [
                'slug' => $slug,
                'label' => __('category.pak.'.$slug),
                'url' => route('index', ['pak' => $slug]),
            ], PublicArticleSnapshot::PAKS),
            'latestMoreUrl' => $selectedPak === null ? route('latest') : route(self::PAK_ROUTES[$selectedPak]),
            'addonSections' => $this->addonSections($articles['pak_addon_counts']),
        ]);
    }

    /**
     * 「種類から探す」の pak ごとの入口。件数があれば多い順に上位を出し、無ければ主な種類をリンクだけで出す
     *
     * @param  array<array-key, array<string, array{total: int, addon_post: int}>>|null  $counts
     * @return list<array{label: string, url: string, addons: list<array{label: string, url: string, count: int|null}>}>
     */
    private function addonSections(?array $counts): array
    {
        /** @var array<string, string> $addonLabels */
        $addonLabels = trans('category.addon');
        $order = array_flip(array_keys($addonLabels));

        $sections = [];
        foreach (PublicArticleSnapshot::PAKS as $pak) {
            if ($counts === null) {
                $addons = array_fill_keys(self::FALLBACK_ADDONS, null);
            } else {
                $addons = array_filter(
                    array_map(fn (array $count): int => $count['total'], array_intersect_key($counts[$pak] ?? [], $addonLabels)),
                    fn (int $count): bool => $count > 0,
                );
                uksort($addons, fn (string $a, string $b): int => [$addons[$b], $order[$a]] <=> [$addons[$a], $order[$b]]);
                $addons = array_slice($addons, 0, self::ADDONS_PER_PAK, true);
            }

            $sections[] = [
                'label' => __('category.pak.'.$pak),
                'url' => route(self::PAK_ROUTES[$pak]),
                'addons' => array_map(fn (string $addon, ?int $count): array => [
                    'label' => $addonLabels[$addon],
                    'url' => route('categories.pakAddon', ['pak' => $pak, 'addon' => $addon]),
                    'count' => $count,
                ], array_keys($addons), array_values($addons)),
            ];
        }

        return $sections;
    }
}
