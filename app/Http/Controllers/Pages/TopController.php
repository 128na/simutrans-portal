<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Front\PublicArticleSnapshot;
use Illuminate\Contracts\View\View;

class TopController extends Controller
{
    public function __construct(
        private readonly PublicArticleSnapshot $snapshot,
    ) {}

    public function top(): View
    {
        $articles = $this->snapshot->read();

        return view('pages.top.index', $articles);
    }
}
