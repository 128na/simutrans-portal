<?php

namespace App\Http\View\Creators;

use App\Repositories\UserRepository;
use Illuminate\View\View;
use App\Models\Article;
use App\Models\Category;
use App\Models\PakAddonCount;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserAddonCount;

/**
 * サイドバーの項目を設定する
 */
class SidebarCreator
{
    private PakAddonCount $pak_addon_count;
    private UserAddonCount $user_addon_count;

    public function __construct(
        PakAddonCount $pak_addon_count,
        UserAddonCount $user_addon_count
    ) {
        $this->pak_addon_count = $pak_addon_count;
        $this->user_addon_count = $user_addon_count;
    }

    /**
     * データをビューと結合
     *
     * @param  View  $view
     * @return void
     */
    public function create(View $view)
    {
        $view->with([
            'menu_user_addon_counts' => $this->getUserAddonCounts(),
            'menu_pak_addon_counts' => $this->getPakAddonCounts(),
        ]);
    }

    /**
     * ユーザー別アドオン投稿数一覧
     */
    private function getUserAddonCounts()
    {
        return $this->user_addon_count->select('user_id', 'user_name', 'count')->get();
    }
    /**
     * pak別アドオン投稿数一覧
     */
    private function getPakAddonCounts()
    {
        return $this->separateByPak(
            $this->pak_addon_count->select('pak_slug', 'addon_slug', 'count')->get()
        );
    }

    private function separateByPak($pak_addon_counts)
    {
        return collect($pak_addon_counts->reduce(function ($separated, $item) {
            if (!isset($separated[$item->pak_slug])) {
                $separated[$item->pak_slug] = collect([]);
            }
            $separated[$item->pak_slug]->push($item);
            return $separated;
        }, []));
    }
}
