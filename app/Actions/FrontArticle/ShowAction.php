<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Models\Article;
use App\Repositories\ArticleRepository;

class ShowAction
{
    public function __construct(private ArticleRepository $articleRepository) {}

    public function __invoke(string $userIdOrNickname, string $slug): ?Article
    {
        $slug = $this->removeJsonExtension($slug);

        return $this->articleRepository->first($userIdOrNickname, $slug);
    }

    private function removeJsonExtension(string $value): string
    {
        if (str_ends_with($value, '.json')) {
            return substr($value, 0, -5);
        }

        return $value;
    }
}
