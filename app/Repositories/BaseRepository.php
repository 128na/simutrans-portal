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
 * @template T of Model
 */
abstract class BaseRepository
{
    /**
     * @var T
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
     *
     * @param  array<mixed>  $data
     */
    public function store(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * 更新.
     *
     * @param  T  $model
     * @param  array<mixed>  $data
     */
    public function update($model, array $data): void
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
     * @param  array<string>  $relations
     *
     * @phpstan-return T
     */
    public function load($model, array $relations = [])
    {
        return $model->loadMissing($relations);
    }

    /**
     * ユーザーのリレーション経由で保存.
     *
     * @param  array<mixed>  $data
     */
    public function storeByUser(User $user, array $data): Model
    {
        return $user->{$this->getRelationName()}()->create($data);
    }

    public function find(int|string|null $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(int|string|null $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param  array<int|string|null>  $ids
     */
    public function findByIds(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    /**
     * @param  array<mixed>  $search
     * @param  array<mixed>  $data
     *
     * @phpstan-return T
     */
    public function updateOrCreate(array $search, array $data = [])
    {
        /** @var T */
        return $this->model->updateOrCreate($search, $data);
    }

    /**
     * @param  array<mixed>  $search
     * @param  array<mixed>  $data
     */
    public function firstOrCreate(array $search, array $data = []): Model
    {
        return $this->model->firstOrCreate($search, $data);
    }

    /**
     * 一覧取得.
     *
     * @param  array<string>  $column
     * @param  array<mixed>  $with
     */
    public function findAll(array $column = ['*'], array $with = [], ?int $limit = null): Collection
    {
        $q = $this->model
            ->select($column)
            ->with($with);
        if ($limit) {
            $q->limit($limit);
        }

        return $q->get();
    }

    /**
     * ページネーションで一覧取得.
     *
     * @param  array<string>  $column
     * @param  array<mixed>  $with
     */
    public function paginate(array $column = ['*'], array $with = [], int $perPage = 24): Paginator
    {
        return $this->model
            ->select($column)
            ->with($with)
            ->paginate($perPage);
    }
}
