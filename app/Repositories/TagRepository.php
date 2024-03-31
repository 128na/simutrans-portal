<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Tag>
 */
class TagRepository extends BaseRepository
{
    private const LIMIT = 10;

    /**
     * @var Tag
     */
    protected $model;

    public function __construct(Tag $tag)
    {
        $this->model = $tag;
    }

    public function getAllTags(): Collection
    {
        return $this->model->select('id', 'name')
            ->whereHas('articles', fn ($query) => $query->active())
            ->popular()
            ->get();
    }

    public function searchTags(string $name, int $limit = self::LIMIT): Collection
    {
        return $this->model->select('id', 'name', 'description')
            ->where('name', 'like', sprintf('%%%s%%', $name))
            ->orWhere('description', 'like', sprintf('%%%s%%', $name))
            ->orderByRaw('LENGTH(name) asc')
            ->limit($limit)
            ->get();
    }

    public function getTags(int $limit = self::LIMIT): Collection
    {
        return $this->model->select('id', 'name', 'description')->limit($limit)
            ->get();
    }

    /**
     * 記事に関連づいていないタグを削除する.
     */
    public function deleteUnrelated(): int
    {
        $tagIds = $this->model
            ->doesntHave('articles')
            ->pluck('id');

        return $this->model->whereIn('id', $tagIds)->delete();
    }
}
