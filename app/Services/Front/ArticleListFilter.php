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
 * - sort: 並び順（modified|published|popular）
 */
final readonly class ArticleListFilter
{
    /** 件数があるとき、種類の切り替えに常に出す種類の数（PC 幅で 1 行に収まる数） */
    public const int ADDON_VISIBLE_LIMIT = 6;

    /**
     * 件数を出せない検索結果で、種類の切り替えに常に出す種類（優先順）。
     *
     * 公開記事の全体件数（3 pak の合計、2026-11 時点）の上位から選んだ。
     * 「その他」は種類を表さないので除き、次に多い港湾施設を入れた。数は ADDON_VISIBLE_LIMIT にそろえる
     */
    public const array SEARCH_PRIMARY_ADDONS = ['trains', 'rail-tools', 'road-tools', 'road-vehicles', 'buildings', 'seaport-tools'];

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

    /**
     * 件数が無い一覧で、決めておいた主要な種類だけを常に出し、残りを「ほか n 種類」に畳む。
     *
     * 件数が分からないので、どの種類も消さずに畳んだ側へ入れる。
     * 選択中の種類が主要な種類に無いときは、主要な種類の最後と入れ替えて常に出す数を変えない。
     * 並びは選択肢の定義順にそろえる。
     *
     * @param  list<string>  $primarySlugs  優先順。選択肢に無い slug は無視する
     * @return array{visible: array<string, string>, folded: array<string, string>}
     */
    public function addonChipGroupsByPrimary(array $primarySlugs): array
    {
        $options = self::addonOptions();
        $visibleSlugs = array_values(array_filter($primarySlugs, fn (string $slug): bool => array_key_exists($slug, $options)));
        if ($this->addonSlug !== null && array_key_exists($this->addonSlug, $options) && ! in_array($this->addonSlug, $visibleSlugs, true)) {
            $visibleSlugs = [...array_slice($visibleSlugs, 0, max(0, count($visibleSlugs) - 1)), $this->addonSlug];
        }

        $visible = array_intersect_key($options, array_flip($visibleSlugs));

        return ['visible' => $visible, 'folded' => array_diff_key($options, $visible)];
    }
}
