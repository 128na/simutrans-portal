<?php

declare(strict_types=1);

namespace App\Models\User;

final class ProfileData
{
    public null|int $avatar;

    public null|string $description;

    public null|string $website;

    /**
     * @param  array{avatar?:int,description?:string,website?:string}  $data
     */
    public function __construct(array $data)
    {
        $id = $data['avatar'] ?? null;
        $this->avatar = $id ? ((int) $id) : null;
        $this->description = $data['description'] ?? null;
        $this->website = $data['website'] ?? null;
    }
}
