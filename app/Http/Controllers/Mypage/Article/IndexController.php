<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\UserShow;
use App\Repositories\Article\MypageArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{
    public function __construct(
        private readonly MypageArticleRepository $mypageArticleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.articles', [
            'user' => new UserShow($user),
            'articles' => $this->mypageArticleRepository->getForMypageList($user),
            'meta' => $this->metaOgpService->mypageArticles(),
        ]);
    }
}
