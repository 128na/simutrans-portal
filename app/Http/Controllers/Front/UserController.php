<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

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
        return view('v2.users.index', [
            'users' => $this->userRepository->getForList(),
            'meta' => $this->metaOgpService->frontUsers(),
        ]);
    }

    public function user(string $userIdOrNickname): View
    {
        $user = $this->userRepository->firstOrFailByIdOrNickname($userIdOrNickname);

        return view('v2.users.show', [
            'user' => $user,
            'articles' => $this->articleRepository->getByUser($user->id),
            'meta' => $this->metaOgpService->frontUser($user),
        ]);
    }
}
