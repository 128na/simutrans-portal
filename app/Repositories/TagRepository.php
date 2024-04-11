<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Tag>
 */
final class TagRepository extends BaseRepository
{
    private const int LIMIT = 10;

    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }

    /**
     * @return Collection<int,Tag>
     */
    public function getAllTags(): Collection
    {
        return $this->model->select('id', 'name')
            ->whereHas('articles', fn ($query) => $query->active())
            ->popular()
            ->get();
    }

    /**
     * @return Collection<int,Tag>
     */
    public function searchTags(string $name, int $limit = self::LIMIT): Collection
    {
        return $this->model->select('id', 'name', 'description')
            ->where('name', 'like', sprintf('%%%s%%', $name))
            ->orWhere('description', 'like', sprintf('%%%s%%', $name))
            ->orderByRaw('LENGTH(name) asc')
            ->limit($limit)
            ->get();
    }

    /**
     * @return Collection<int,Tag>
     */
    public function getTags(int $limit = self::LIMIT): Collection
    {
        return $this->model->select('id', 'name', 'description')->limit($limit)
            ->get();
    }
}
