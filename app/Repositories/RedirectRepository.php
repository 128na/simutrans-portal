<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Redirect;
use Illuminate\Database\Eloquent\Collection;

class RedirectRepository
{
    public function __construct(public Redirect $model) {}

    public function findOrFailByPath(string $path): Redirect
    {
        return $this->model->from($path)->firstOrFail();
    }

    /**
     * @return Collection<int,Redirect>
     */
    public function findByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * @param  array{user_id?:number,from:string,to:string}  $data
     */
    public function store(array $data): Redirect
    {
        return $this->model->create($data);
    }
}
