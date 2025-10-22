<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class FrontMiscController extends Controller
{
    public function social()
    {
        return view('v2.social.index', []);
    }

    // 特定ページへの固定リダイレクト
    const REDIRECT_MAP = [
        'simutrans-interact-meeting' => 1212,
    ];
    public function redirect(string $name)
    {
        if (array_key_exists($name, self::REDIRECT_MAP)) {
            return redirect()
                ->route('articles.fallbackShow', ['id' => self::REDIRECT_MAP[$name]], 302);
        }
    }

    public function fallback(Request $request)
    {
        return view('v2.top');
    }
}
