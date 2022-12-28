<?php

namespace App\Repositories\User;

use App\Models\User\Profile;
use App\Repositories\BaseRepository;

/**
 * @extends BaseRepository<Profile>
 */
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
