<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\RedirectRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * 旧サイト -> 新サイトのリダイレクトを行う.
 */
class RedirectController extends Controller
{
    public function __construct(private readonly RedirectRepository $redirectRepository)
    {
    }

    public function index(Request $request): RedirectResponse
    {
        $path = $this->getRelativePath($request->fullUrl());
        $redirect = $this->redirectRepository->findOrFailByPath($path);
        logger("[redirect]: {$redirect->from} -> {$redirect->to}");

        return redirect($redirect->to, Response::HTTP_MOVED_PERMANENTLY);
    }

    private function getRelativePath(string $fullUrl): string
    {
        return str_replace(config('app.url'), '', $fullUrl);
    }
}
