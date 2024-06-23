<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Http\Resources\Api\Mypage\Articles as ArticlesResouce;
use App\Models\User;
use App\Repositories\ArticleRepository;

final readonly class FindArticle
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {}

    public function __invoke(User $user): ArticlesResouce
    {
        return new ArticlesResouce(
            $this->articleRepository->findAllByUser($user, ArticleRepository::MYPAGE_RELATIONS)
        );
    }
}
