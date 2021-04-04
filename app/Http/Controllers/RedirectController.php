<?php

namespace App\Http\Controllers;

use App\Repositories\RedirectRepository;
use Illuminate\Http\Request;

/**
 * 旧サイト -> 新サイトのリダイレクトを行う.
 */
class RedirectController extends Controller
{
    private RedirectRepository $redirectRepository;

    public function __construct(RedirectRepository $redirectRepository)
    {
        $this->redirectRepository = $redirectRepository;
    }

    public function index(Request $request)
    {
        $path = $this->getRelativePath($request->fullUrl());
        $redirect = $this->redirectRepository->findOrFailByPath($path);
        logger("[redirect]: {$redirect->from} -> {$redirect->to}");

        return redirect($redirect->to, 301);
    }

    private function getRelativePath(string $fullUrl): string
    {
        return str_replace(config('app.url'), '', $fullUrl);
    }
}
