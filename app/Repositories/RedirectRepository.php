<?php

namespace App\Repositories;

use App\Models\Redirect;

class RedirectRepository extends BaseRepository
{
    public function __construct(Redirect $model)
    {
        $this->model = $model;
    }

    public function findOrFailByPath(string $path): Redirect
    {
        return $this->model->from($path)->firstOrFail();
    }
}
