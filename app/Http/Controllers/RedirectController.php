<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Redirect\DoRedirectIfExists;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * 旧サイト -> 新サイトのリダイレクトを行う.
 */
class RedirectController extends Controller
{
    // 特定ページへの固定リダイレクト
    public const array REDIRECT_MAP = [
        'simutrans-interact-meeting' => 1212,
    ];

    public function index(Request $request, DoRedirectIfExists $doRedirectIfExists): RedirectResponse
    {
        return $doRedirectIfExists($request->fullUrl());
    }

    public function redirect(string $name): RedirectResponse
    {
        if (array_key_exists($name, self::REDIRECT_MAP)) {
            return to_route('articles.fallbackShow', ['id' => self::REDIRECT_MAP[$name]], 302);
        }

        return to_route('index');
    }
}
