<?php

declare(strict_types=1);

namespace App\Services\Front;

use App\Repositories\TagRepository;
use Illuminate\Database\Eloquent\Collection;

final readonly class TagService
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {}

    /**
     * @return Collection<int, \App\Models\Tag>
     */
    public function getAllTags(): Collection
    {
        return $this->tagRepository->getAllTags();
    }
}
