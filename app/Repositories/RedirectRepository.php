<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Redirect;

/**
 * @extends BaseRepository<Redirect>
 */
class RedirectRepository extends BaseRepository
{
    /**
     * @var Redirect
     */
    protected $model;

    public function __construct(Redirect $redirect)
    {
        $this->model = $redirect;
    }

    public function findOrFailByPath(string $path): Redirect
    {
        return $this->model->from($path)->firstOrFail();
    }
}
