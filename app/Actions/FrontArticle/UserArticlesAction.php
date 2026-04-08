<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\UserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class UserArticlesAction
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private UserRepository $userRepository,
    ) {}

    /**
     * @return array{user: User, articles: LengthAwarePaginator<int, \App\Models\Article>}
     */
    public function __invoke(string $userIdOrNickname, int $limit = 24): array
    {
        $user = $this->userRepository->firstOrFailByIdOrNickname($userIdOrNickname);
        $articles = $this->articleRepository->getByUser($user->id, $limit);

        return [
            'user' => $user,
            'articles' => $articles,
        ];
    }
}
