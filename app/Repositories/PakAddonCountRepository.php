<?php

namespace App\Repositories;

use App\Models\PakAddonCount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PakAddonCountRepository extends BaseRepository
{
    private const DELETE_SQL = 'DELETE FROM pak_addon_counts';
    private const INSERT_SQL = "INSERT INTO pak_addon_counts (pak_slug, addon_slug, count) (
        SELECT
            pak.slug pak_slug,
            addon.slug addon_slug,
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
                AND addon.id IS NOT NULL
                AND u.deleted_at IS NULL
        GROUP BY pak.id , addon.id
        ORDER BY pak.order , addon.order)";

    /**
     * @var PakAddonCount
     */
    protected $model;

    public function __construct(PakAddonCount $model)
    {
        $this->model = $model;
    }

    /**
     * 再集計する.
     */
    public function recount()
    {
        DB::transaction(function () {
            DB::statement(self::DELETE_SQL);
            DB::statement(self::INSERT_SQL);
        });
    }

    public function get(): Collection
    {
        return $this->model->select('pak_slug', 'addon_slug', 'count')->get();
    }
}
