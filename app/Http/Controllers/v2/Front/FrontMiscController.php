<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2\Front;

use App\Services\Front\MetaOgpService;
use Illuminate\Routing\Controller;

final class FrontMiscController extends Controller
{
    // 特定ページへの固定リダイレクト
    public const array REDIRECT_MAP = [
        'simutrans-interact-meeting' => 1212,
    ];

    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function social(): \Illuminate\Contracts\View\View
    {
        return view('v2.social.index', [
            'meta' => $this->metaOgpService->social(),
        ]);
    }

    public function redirect(string $name): \Illuminate\Http\RedirectResponse
    {
        if (array_key_exists($name, self::REDIRECT_MAP)) {
            return to_route('articles.fallbackShow', ['id' => self::REDIRECT_MAP[$name]], 302);
        }

        return to_route('index');
    }
}
