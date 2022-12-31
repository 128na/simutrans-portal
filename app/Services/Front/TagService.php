<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Repositories\TagRepository;
use App\Services\Service;
use Illuminate\Database\Eloquent\Collection;

class TagService extends Service
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @return Collection<int, \App\Models\Tag>
     */
    public function getAllTags(): Collection
    {
        return $this->tagRepository->getAllTags();
    }
}
