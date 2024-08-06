<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Redirect\DoRedirectIfExists;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * 旧サイト -> 新サイトのリダイレクトを行う.
 */
final class RedirectController extends Controller
{
    public function index(Request $request, DoRedirectIfExists $doRedirectIfExists): RedirectResponse
    {
        return $doRedirectIfExists($request->fullUrl());
    }
}
