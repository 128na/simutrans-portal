<?php

declare(strict_types=1);

namespace App\Repositories\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Repository に基本的な CRUD 操作を提供する Trait
 *
 * 各 Repository で共通するデータ操作メソッドを提供します。
 * 各 Repository は必要に応じてこの Trait を use してください。
 *
 * @example
 * ```php
 * class MyListRepository
 * {
 *     use HasCrud;
 *
 *     public function __construct(
 *         private readonly MyList $model,
 *     ) {}
 * }
 * ```
 */
trait HasCrud
{
    /**
     * レコードを作成
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * レコードを更新
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Model $model, array $data): void
    {
        $model->update($data);
    }

    /**
     * レコードを削除
     */
    public function delete(Model $model): void
    {
        $model->delete();
    }

    /**
     * ID でレコードを取得
     */
    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * ID でレコードを取得（存在しない場合は例外）
     */
    public function findByIdOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }
}
