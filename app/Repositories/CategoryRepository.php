<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    public function __construct(public Category $model) {}

    /**
     * @return Collection<int,Category>
     */
    public function getForSearch(): Collection
    {
        return $this->model->query()
            ->select(['categories.id', 'categories.type', 'categories.slug', 'categories.need_admin'])
            ->orderBy('order', 'asc')
            ->get();
    }

    public function getByTypeSlug(CategoryType $categoryType, string $slug): Category
    {
        return $this->model->query()
            ->where('type', $categoryType->value)
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * @return SupportCollection<string,SupportCollection<int,\stdClass>>
     */
    public function getForPakAddonList(): SupportCollection
    {
        return DB::table('articles as a')
            ->join('article_category as ac_pak', 'ac_pak.article_id', '=', 'a.id')
            ->join('categories as pak', function ($join): void {
                $join->on('pak.id', '=', 'ac_pak.category_id')
                    ->where('pak.type', 'pak')
                    ->whereIn('pak.slug', ['128-japan', '128', '64']);
            })
            ->join('article_category as ac_addon', 'ac_addon.article_id', '=', 'a.id')
            ->join('categories as addon', function ($join): void {
                $join->on('addon.id', '=', 'ac_addon.category_id')
                    ->where('addon.type', 'addon');
            })
            ->select([
                'pak.slug AS pak_slug',
                'addon.slug AS addon_slug',
                DB::raw('COUNT(*) AS article_count'),
            ])
            ->groupBy('pak.slug', 'addon.slug')
            ->orderBy('pak.order')
            ->orderBy('addon.order')
            ->get()
            ->groupBy('pak_slug');
    }
}
