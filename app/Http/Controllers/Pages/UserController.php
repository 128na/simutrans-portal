<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ArticleCard;
use App\Http\Resources\Frontend\UserShow;
use App\Repositories\Article\FrontArticleRepository;
use App\Repositories\UserRepository;
use App\Services\Front\ArticleListFilter;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly FrontArticleRepository $articleRepository,
        private readonly UserRepository $userRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function users(): View
    {
        return view('pages.users.index', [
            'users' => $this->userRepository->getForList(),
            'meta' => $this->metaOgpService->frontUsers(),
        ]);
    }

    public function user(Request $request, string $userIdOrNickname): View
    {
        $filter = ArticleListFilter::fromRequest($request);
        $user = $this->userRepository->firstOrFailByIdOrNickname($userIdOrNickname);

        return view('pages.users.show', [
            'user' => new UserShow($user),
            'articles' => ArticleCard::collection($this->articleRepository->getByUser($user->id, filter: $filter)),
            'filter' => $filter,
            'meta' => $this->metaOgpService->frontUser($user),
        ]);
    }
}
