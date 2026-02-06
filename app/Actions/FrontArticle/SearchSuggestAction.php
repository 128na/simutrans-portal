<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Models\Tag;
use App\Models\User;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class SearchSuggestAction
{
    public function __construct(
        private TagRepository $tagRepository,
        private UserRepository $userRepository,
    ) {}

    /**
     * @return Collection<int, Tag>
     */
    public function tags(string $keyword, int $limit = 20): Collection
    {
        return $this->tagRepository->getForSuggest($keyword, $limit);
    }

    /**
     * @return Collection<int, User>
     */
    public function users(string $keyword, int $limit = 20): Collection
    {
        return $this->userRepository->getForSuggest($keyword, $limit);
    }
}
