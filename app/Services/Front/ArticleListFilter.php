<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Enums\ArticleListSort;
use App\Enums\ArticlePostType;
use Illuminate\Http\Request;

/**
 * 公開側のアドオン一覧の絞り込み・並び順（URLのクエリで共有する）
 *
 * - addon: 種類（addon カテゴリの slug）。選択肢は lang の category.addon から作り、DB を引かない
 * - postTypes[]=addon-post: ダウンロードできるもの（アドオン投稿）だけ。検索画面と同じ形にそろえる
 * - sort: 並び順（modified|published）
 */
final readonly class ArticleListFilter
{
    /** 件数があるとき、種類の切り替えに常に出す種類の数（PC 幅で 1 行に収まる数） */
    public const int ADDON_VISIBLE_LIMIT = 6;

    public function __construct(
        public ?string $addonSlug = null,
        public bool $downloadOnly = false,
        public ArticleListSort $sort = ArticleListSort::Modified,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $addon = $request->query('addon');
        $postTypes = $request->query('postTypes');
        $sort = $request->query('sort');

        return new self(
            addonSlug: is_string($addon) && array_key_exists($addon, self::addonOptions()) ? $addon : null,
            downloadOnly: is_array($postTypes) && array_values($postTypes) === [ArticlePostType::AddonPost->value],
            sort: (is_string($sort) ? ArticleListSort::tryFrom($sort) : null) ?? ArticleListSort::Modified,
        );
    }

    /**
     * 種類がすでに決まっている一覧（pak×種類）用に、種類の絞り込みを外す
     */
    public function withoutAddon(): self
    {
        return new self(downloadOnly: $this->downloadOnly, sort: $this->sort);
    }

    /**
     * 種類の選択肢（slug => 表示名）
     *
     * @return array<string, string>
     */
    public static function addonOptions(): array
    {
        /** @var array<string, string> $options */
        $options = trans('category.addon');

        return $options;
    }

    /**
     * 種類の切り替えに並べる選択肢を、常に出すものと「ほか n 種類」に畳むものに分ける。
     *
     * 件数（slug => 件数）があるときは、0 件の種類を出さず、件数の多い上位 $visibleLimit 種類だけを常に出す。
     * 選択中の種類は件数が少なくても 0 件でも常に出す（上位の最後と入れ替える）。件数が無い一覧では、少ない種類が分からないので畳まない。
     * どちらも並びは選択肢の定義順にそろえる。
     *
     * @param  array<string, int>|null  $counts
     * @return array{visible: array<string, string>, folded: array<string, string>}
     */
    public function addonChipGroups(?array $counts, int $visibleLimit = self::ADDON_VISIBLE_LIMIT): array
    {
        $options = self::addonOptions();

        if ($counts === null) {
            return ['visible' => $options, 'folded' => []];
        }

        $listed = array_filter(
            $options,
            fn (string $slug): bool => ($counts[$slug] ?? 0) > 0 || $slug === $this->addonSlug,
            ARRAY_FILTER_USE_KEY,
        );

        $ranked = array_keys($listed);
        // usort は安定ソートなので、件数が同じなら定義順を保つ
        usort($ranked, fn (string $a, string $b): int => ($counts[$b] ?? 0) <=> ($counts[$a] ?? 0));
        $visibleSlugs = array_slice($ranked, 0, max(0, $visibleLimit));
        // 選択中が上位に入らないときは、上位の最後と入れ替えて常に出す数を変えない（1 行に収めるため）
        if ($this->addonSlug !== null && array_key_exists($this->addonSlug, $listed) && ! in_array($this->addonSlug, $visibleSlugs, true)) {
            $visibleSlugs = [...array_slice($visibleSlugs, 0, max(0, $visibleLimit - 1)), $this->addonSlug];
        }

        $visible = array_intersect_key($listed, array_flip($visibleSlugs));

        return ['visible' => $visible, 'folded' => array_diff_key($listed, $visible)];
    }
}
