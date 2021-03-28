<?php

namespace App\Http\View\Creators;

use App\Repositories\PakAddonCountRepository;
use App\Repositories\UserAddonCountRepository;
use Illuminate\View\View;

/**
 * サイドバーの項目を設定する.
 */
class SidebarCreator
{
    private PakAddonCountRepository $pakAddonCountRepository;
    private UserAddonCountRepository $userAddonCountRepository;

    public function __construct(
        PakAddonCountRepository $pakAddonCountRepository,
        UserAddonCountRepository $userAddonCountRepository
    ) {
        $this->pakAddonCountRepository = $pakAddonCountRepository;
        $this->userAddonCountRepository = $userAddonCountRepository;
    }

    /**
     * データをビューと結合.
     *
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
     * ユーザー別アドオン投稿数一覧.
     */
    private function getUserAddonCounts()
    {
        return $this->userAddonCountRepository->get();
    }

    /**
     * pak別アドオン投稿数一覧.
     */
    private function getPakAddonCounts()
    {
        return $this->separateByPak(
            $this->pakAddonCountRepository->get()
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
