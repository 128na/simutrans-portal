<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * リポジトリクラス.
 *
 * 単体： find(OrFail)(By)Hoge
 * 一覧： findAll(By)Hoge
 * ページネーション: paginate(By)Hoge
 * カーソル: cursor(By)Hoge
 *
 * @template T
 */
abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * モデルの複数形名称を返す.
     */
    protected function plural(): string
    {
        return Str::plural(class_basename($this->model));
    }

    /**
     * モデルの単数形名称を返す.
     */
    protected function singular(): string
    {
        return Str::singular(class_basename($this->model));
    }

    protected function getRelationName(): string
    {
        return $this->plural();
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
     *
     * @param  T  $model
     * @return T
     */
    public function load($model, array $relations = [])
    {
        return $model->loadMissing($relations);
    }

    /**
     * ユーザーのリレーション経由で保存.
     */
    public function storeByUser(User $user, array $data): Model
    {
        return $user->{$this->getRelationName()}()->create($data);
    }

    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail($id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function findByIds(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    public function updateOrCreate(array $search, array $data = [])
    {
        return $this->model->updateOrCreate($search, $data);
    }

    public function firstOrCreate(array $search, array $data = [])
    {
        return $this->model->firstOrCreate($search, $data);
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
