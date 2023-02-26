<?php

declare(strict_types=1);

namespace App\Models\User;

class ProfileData
{
    public ?int $avatar;

    public ?string $description;

    public ?string $website;

    public ?string $twitter;

    public ?string $gtag;

    /**
     * @param  array<mixed>  $data
     */
    public function __construct(array $data)
    {
        $id = $data['avatar'] ?? null;
        $this->avatar = $id ? (int) $id : null;
        $this->description = $data['description'] ?? null;
        $this->website = $data['website'] ?? null;
        $this->twitter = $data['twitter'] ?? null;
        $this->gtag = $data['gtag'] ?? null;
    }
}
