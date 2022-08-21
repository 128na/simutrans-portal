<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Collection;

class TagRepository extends BaseRepository
{
    private const LIMIT = 10;
    /**
     * @var Tag
     */
    protected $model;

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    public function getIdsByNames(array $tagNames): Collection
    {
        $tags = collect($tagNames)
            ->map(fn (string $tagName) => $this->model->firstOrCreate(['name' => $tagName]));

        return $tags->pluck('id');
    }

    public function getAllTags(): Collection
    {
        return $this->model->select('id', 'name')
            ->whereHas('articles', fn ($query) => $query->active())
            ->popular('articles_count', 'desc')
            ->withCache()
            ->get();
    }

    public function searchTags(string $name, int $limit = self::LIMIT)
    {
        return $this->model->select('id', 'name')->where('name', 'like', "%{$name}%")
            ->orderByRaw('LENGTH(name) asc')
            ->limit($limit)
            ->get();
    }

    public function getTags(int $limit = self::LIMIT)
    {
        return $this->model->select('id', 'name')->limit($limit)
            ->get();
    }

    /**
     * 記事に関連づいていないタグを削除する.
     */
    public function deleteUnrelated(): int
    {
        return $this->model->leftJoin('article_tag', 'tags.id', '=', 'article_tag.tag_id')
            ->whereNull('article_id')
            ->delete();
    }
}
