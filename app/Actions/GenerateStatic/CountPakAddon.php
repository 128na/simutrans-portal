<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class CountPakAddon
{
    private const string SELECT_SQL = "SELECT
            pak.slug pak_slug,
            case addon.slug is null when 1 then 'none' else addon.slug end addon_slug,
            COUNT(a.id) count
        FROM
            articles a
        LEFT JOIN users u ON u.id = a.user_id
        LEFT JOIN (
            SELECT
                a.id article_id, c.id, c.slug, c.order
            FROM
                categories c
            LEFT JOIN article_category ac ON ac.category_id = c.id AND c.type = 'pak'
            LEFT JOIN articles a ON a.id = ac.article_id
                AND a.status = 'publish'
                AND a.deleted_at IS NULL
        ) pak ON pak.article_id = a.id
        LEFT JOIN (
            SELECT
                a.id article_id, c.id, c.slug, c.order
            FROM
                categories c
            LEFT JOIN article_category ac ON ac.category_id = c.id
                AND c.type = 'addon'
            LEFT JOIN articles a ON a.id = ac.article_id
                AND a.status = 'publish'
                AND a.deleted_at IS NULL
        ) addon ON addon.article_id = a.id
        WHERE
            a.post_type IN ('addon-post', 'addon-introduction')
                AND pak.id IS NOT NULL
                AND u.deleted_at IS NULL
        GROUP BY pak.id , addon.id
        ORDER BY pak.order , case addon.order is null when 1 then 2147483647 else addon.order end";

    /**
     * @return array<string,array<int,array{pak_slug:string,addon_slug:string,pak:string,addon:string,count:int}>>
     */
    public function __invoke(): array
    {
        /** @var Collection<int,object{pak_slug:string,addon_slug:string,count:int}> */
        $items = collect(DB::select(self::SELECT_SQL));

        /**
         * @param  object{pak_slug:string,addon_slug:string,count:int}  $pakAddonCount
         */
        $items = $items->map(fn ($pakAddonCount): array => [
            'pak_slug' => $pakAddonCount->pak_slug,
            'addon_slug' => $pakAddonCount->addon_slug,
            'pak' => __('category.pak.'.$pakAddonCount->pak_slug),
            'addon' => __('category.addon.'.$pakAddonCount->addon_slug),
            'count' => $pakAddonCount->count,
        ]);

        /**
         * @var array<string,array<int,array{pak_slug:string,addon_slug:string,pak:string,addon:string,count:int}>>
         */
        return $items->groupBy('pak')->toArray();
    }
}
