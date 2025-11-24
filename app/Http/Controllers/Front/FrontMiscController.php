<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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

    public function social(): View
    {
        return view('pages.social.index', [
            'meta' => $this->metaOgpService->frontSocial(),
        ]);
    }

    public function redirect(string $name): RedirectResponse
    {
        if (array_key_exists($name, self::REDIRECT_MAP)) {
            return to_route('articles.fallbackShow', ['id' => self::REDIRECT_MAP[$name]], 302);
        }

        return to_route('index');
    }
}
