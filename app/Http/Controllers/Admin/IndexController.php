<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function flushCache()
    {
        Cache::flush();
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');

        session()->flash('success', 'キャッシュをクリアしました');
        return redirect()->route('admin.index');
    }

    public function error()
    {
        trigger_error("Error was created manually.", E_USER_ERROR);
    }

    public function warning()
    {
        trigger_error("Warning was created manually.", E_USER_WARNING);
    }

    public function notice()
    {
        trigger_error("Notice was created manually.", E_USER_NOTICE);
    }

}
