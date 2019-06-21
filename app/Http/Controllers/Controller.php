<?php

namespace App\Http\Controllers;

use App\Models\UserAddonCount;
use App\Models\PakAddonCount;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * ヘッダーデータを追加してビューを返す
     * @param string $view_path
     * @param array|void $data
     * @return Illuminate\View\View
     */
    protected static function viewWithHeader($view_path, $data = [])
    {
        $data['menu_user_addon_counts'] = UserAddonCount::all();

        $pak_addon_counts = PakAddonCount::all();
        $data['menu_pak_addon_counts'] = self::separateByPak($pak_addon_counts);
        return view($view_path, $data);
    }

    protected static function separateByPak($pak_addon_counts)
    {
        return collect($pak_addon_counts->reduce(function($separated, $item) {
            if (!isset($separated[$item->pak_name])) {
                $separated[$item->pak_name] = collect([]);
            }
            $separated[$item->pak_name]->push($item);
            return $separated;
        }, []));
    }
}
