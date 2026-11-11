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
}
