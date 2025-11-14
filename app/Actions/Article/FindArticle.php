<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Http\Resources\ArticleList;
use App\Models\User;
use App\Repositories\ArticleRepository;

final readonly class FindArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {}

    public function __invoke(User $user): ArticleList
    {
        return new ArticleList(
            $this->articleRepository->findAllByUser($user, ArticleRepository::MYPAGE_RELATIONS)
        );
    }
}
