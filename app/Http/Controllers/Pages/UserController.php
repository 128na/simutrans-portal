<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Resources\Frontend\ArticleList;
use App\Http\Resources\Frontend\UserShow;
use App\Repositories\ArticleRepository;
use App\Repositories\UserRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class UserController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
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

    public function user(string $userIdOrNickname): View
    {
        $user = $this->userRepository->firstOrFailByIdOrNickname($userIdOrNickname);

        return view('pages.users.show', [
            'user' => new UserShow($user),
            'articles' => ArticleList::collection($this->articleRepository->getByUser($user->id)),
            'meta' => $this->metaOgpService->frontUser($user),
        ]);
    }
}
