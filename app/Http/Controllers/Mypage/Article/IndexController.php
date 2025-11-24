<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class IndexController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        return view('mypage.articles', [
            'user' => $user->only(['id', 'name', 'nickname']),
            'articles' => $user
                ->articles()
                ->select('id', 'title', 'slug', 'status', 'post_type', 'published_at', 'modified_at')
                ->with('attachments', 'totalConversionCount', 'totalViewCount')
                ->get(),
            'meta' => $this->metaOgpService->mypageArticles(),
        ]);
    }
}
