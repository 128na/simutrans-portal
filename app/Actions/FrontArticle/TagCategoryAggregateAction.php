<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Models\Tag;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class TagCategoryAggregateAction
{
    public function __construct(
        private TagRepository $tagRepository,
        private CategoryRepository $categoryRepository,
    ) {}

    /**
     * @return Collection<int, Tag>
     */
    public function tags(): Collection
    {
        return $this->tagRepository->getForList();
    }

    /**
     * @return SupportCollection<string, SupportCollection<int, \stdClass>>
     */
    public function pakAddonCategories(): SupportCollection
    {
        return $this->categoryRepository->getForPakAddonList();
    }
}
