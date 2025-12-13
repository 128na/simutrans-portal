<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Http\Resources\Frontend\UserShow;
use App\Repositories\ArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        return view('mypage.articles', [
            'user' => new UserShow($user),
            'articles' => $this->articleRepository->getForMypageList($user),
            'meta' => $this->metaOgpService->mypageArticles(),
        ]);
    }
}
