<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class FrontMiscController extends Controller
{
    // 特定ページへの固定リダイレクト
    public const array REDIRECT_MAP = [
        'simutrans-interact-meeting' => 1212,
    ];

    public function social(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('v2.social.index', []);
    }

    public function redirect(string $name)
    {
        if (array_key_exists($name, self::REDIRECT_MAP)) {
            return to_route('articles.fallbackShow', ['id' => self::REDIRECT_MAP[$name]], 302);
        }

        return null;
    }

    public function fallback(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('v2.top');
    }
}
