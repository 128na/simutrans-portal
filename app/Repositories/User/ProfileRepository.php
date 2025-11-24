<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User\Profile;

final class ProfileRepository
{
    public function __construct(private readonly Profile $model) {}

    /**
     * @param  array<mixed>  $data
     */
    public function store(array $data): Profile
    {
        return $this->model->create($data);
    }

    /**
     * @param  array<mixed>  $data
     */
    public function update(Profile $profile, array $data): void
    {
        $profile->update($data);
    }

    public function find(int|string|null $id): ?Profile
    {
        return $this->model->find($id);
    }

    public function delete(Profile $profile): void
    {
        $profile->delete();
    }
}
