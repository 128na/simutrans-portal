<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\RedirectRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

/**
 * 旧サイト -> 新サイトのリダイレクトを行う.
 */
final class RedirectController extends Controller
{
    public function __construct(private readonly RedirectRepository $redirectRepository) {}

    public function index(Request $request): RedirectResponse
    {
        $path = $this->getRelativePath($request->fullUrl());
        $redirect = $this->redirectRepository->findOrFailByPath($path);
        logger(sprintf('[RedirectController]: %s -> %s', $redirect->from, $redirect->to));

        return redirect($redirect->to, Response::HTTP_MOVED_PERMANENTLY);
    }

    private function getRelativePath(string $fullUrl): string
    {
        return str_replace(Config::string('app.url'), '', $fullUrl);
    }
}
