<?php

namespace App\Http\Controllers;

use App\Models\Redirect;
use Illuminate\Http\Request;

/**
 * 旧サイト -> 新サイトのリダイレクトを行う
 */
class RedirectController extends Controller
{
    public function index(Request $request)
    {
        $path = self::getFullPath($request);
        $redirect = Redirect::from($path)->firstOrFail();

        return redirect($redirect->to, 301);
    }

    private function getFullPath($request)
    {
        return str_replace(config('app.url'), '', $request->fullUrl());
    }
}
