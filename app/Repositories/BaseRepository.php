<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * モデルの複数形名称を返す.
     */
    private function plural(): string
    {
        return Str::plural(class_basename($this->model));
    }

    /**
     * 保存.
     */
    public function store(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * 更新.
     */
    public function update(Model $model, array $data): void
    {
        $model->update($data);
    }

    /**
     * 削除.
     */
    public function delete(Model $model): void
    {
        $model->delete();
    }

    /**
     * リレーションをロード.
     */
    public function load(Model $model, array $relations = []): Model
    {
        return $model->loadMissing($relations);
    }

    /**
     * ユーザーのリレーション経由で保存.
     */
    public function storeByUser(User $user, array $data): Model
    {
        return $user->{$this->plural()}()->create($data);
    }

    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    public function findByIds(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    /**
     * 一覧取得.
     */
    public function findAll(array $column = ['*'], array $with = [], ?int $limit = null): Collection
    {
        return $this->model
            ->select($column)
            ->with($with)
            ->limit($limit)
            ->get();
    }

    /**
     * ページネーションで一覧取得.
     */
    public function paginate(array $column = ['*'], array $with = [], int $perPage = 24): Paginator
    {
        return $this->model
            ->select($column)
            ->with($with)
            ->paginate($perPage);
    }
}
