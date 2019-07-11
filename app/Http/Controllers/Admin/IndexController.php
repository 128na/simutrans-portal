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

}
