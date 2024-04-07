<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User\Profile;
use App\Repositories\BaseRepository;

/**
 * @extends BaseRepository<Profile>
 */
final class ProfileRepository extends BaseRepository
{
    public function __construct(Profile $profile)
    {
        parent::__construct($profile);
    }
}
