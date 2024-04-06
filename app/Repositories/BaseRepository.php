<?php

declare(strict_types=1);

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
     * @param  T  $model
     */
    public function __construct(protected Model $model)
    {
    }

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
     * @return T
     */
    public function store(array $data)
    {
        /** @var T */
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
     * @return T
     */
    public function load($model, array $relations = [])
    {
        return $model->loadMissing($relations);
    }

    /**
     * ユーザーのリレーション経由で保存.
     *
     * @param  array<mixed>  $data
     * @return T
     */
    public function storeByUser(User $user, array $data)
    {
        /** @var T */
        return $user->{$this->getRelationName()}()->create($data);
    }

    /**
     * @return T|null
     */
    public function find(int|string|null $id)
    {
        /** @var T|null */
        return $this->model->find($id);
    }

    /**
     * @return T
     */
    public function findOrFail(int|string|null $id): Model
    {
        /** @var T */
        return $this->model->findOrFail($id);
    }

    /**
     * @param  array<int|string|null>  $ids
     * @return Collection<int,T>
     */
    public function findByIds(array $ids): Collection
    {
        /** @var Collection<int,T> */
        return $this->model->whereIn('id', $ids)->get();
    }

    /**
     * @param  array<mixed>  $search
     * @param  array<mixed>  $data
     * @return T
     */
    public function updateOrCreate(array $search, array $data = [])
    {
        /** @var T */
        return $this->model->updateOrCreate($search, $data);
    }

    /**
     * @param  array<mixed>  $search
     * @param  array<mixed>  $data
     * @return T
     */
    public function firstOrCreate(array $search, array $data = [])
    {
        /** @var T */
        return $this->model->firstOrCreate($search, $data);
    }

    /**
     * 一覧取得.
     *
     * @param  array<string>  $column
     * @param  array<mixed>  $with
     * @return Collection<int,T>
     */
    public function findAll(array $column = ['*'], array $with = [], ?int $limit = null): Collection
    {
        $q = $this->model
            ->select($column)
            ->with($with);
        if ($limit !== null && $limit !== 0) {
            $q->limit($limit);
        }

        /** @var Collection<int,T> */
        return $q->get();
    }

    /**
     * ページネーションで一覧取得.
     *
     * @param  array<string>  $column
     * @param  array<mixed>  $with
     * @return Paginator<T>
     */
    public function paginate(array $column = ['*'], array $with = [], int $perPage = 24): Paginator
    {
        /** @var Paginator<T> */
        return $this->model
            ->select($column)
            ->with($with)
            ->paginate($perPage);
    }
}
