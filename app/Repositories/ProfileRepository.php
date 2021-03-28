<?php

namespace App\Repositories;

use App\Models\Profile;

class ProfileRepository extends BaseRepository
{
    /**
     * @var Profile
     */
    protected $model;

    public function __construct(Profile $model)
    {
        $this->model = $model;
    }
}
