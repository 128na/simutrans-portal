<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User\Profile;

class ProfileRepository
{
    public function __construct(private Profile $profile) {}

    /**
     * @param  array<mixed>  $data
     */
    public function store(array $data): Profile
    {
        return $this->profile->create($data);
    }

    /**
     * @param  array<mixed>  $data
     */
    public function update(Profile $profile, array $data): void
    {
        $profile->update($data);
    }

    public function find(null|int|string $id): ?Profile
    {
        return $this->profile->find($id);
    }

    public function delete(Profile $profile): void
    {
        $profile->delete();
    }
}
