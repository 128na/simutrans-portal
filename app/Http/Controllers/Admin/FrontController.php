<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class FrontController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function flushCache()
    {
        Redis::flushAll();
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');

        session()->flash('success', 'キャッシュをクリアしました');
        return redirect()->route('admin.index');
    }

}
