<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class TagRepository
{
    public function __construct(public Tag $model) {}

    /**
     * 検索用のタグ一覧を取得する
     *
     * @return Collection<int,Tag>
     */
    public function getForSearch(): Collection
    {
        return $this->model->query()
            ->select(['tags.id', 'tags.name'])
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * 編集用のタグ一覧を取得する
     *
     * @return Collection<int,Tag>
     */
    public function getForEdit(): Collection
    {
        return $this->model->query()
            ->select('tags.*', DB::raw('COUNT(at.article_id) AS articles_count'))
            ->leftJoin('article_tag as at', 'tags.id', '=', 'at.tag_id')
            ->groupBy('tags.id')
            ->orderBy('name', 'asc')
            ->with('createdBy:id,name', 'lastModifiedBy:id,name')
            ->get();
    }

    /**
     * 一覧表示用のタグ一覧を取得する
     *
     * @return Collection<int,Tag>
     */
    public function getForList(): Collection
    {
        return $this->model->query()
            ->select(['tags.id', 'tags.name', DB::raw('COUNT(article_tag.article_id) as articles_count')])
            ->join('article_tag', 'tags.id', '=', 'article_tag.tag_id')
            ->groupBy('tags.id', 'tags.name')
            ->orderByRaw('COUNT(article_tag.article_id) DESC')
            ->get();
    }

    public function load(Tag $tag): Tag
    {
        return $tag
            ->loadCount('articles')
            ->loadMissing('createdBy:id,name', 'lastModifiedBy:id,name');
    }

    /**
     * @param  array{name:string,description:string|null,created_by:int,last_modified_at:\Carbon\CarbonImmutable,last_modified_by:int}  $data
     */
    public function store(array $data): Tag
    {
        return $this->model->create($data);
    }

    /**
     * @param  array{description:string|null,last_modified_at:\Carbon\CarbonImmutable,last_modified_by:int}  $data
     */
    public function update(Tag $tag, array $data): Tag
    {
        return tap($tag, fn($t) => $t->update($data));
    }
}
