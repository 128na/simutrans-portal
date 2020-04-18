<?php

namespace App\Models\Contents;

class ProfileData
{
    public $avatar;
    public $description;
    public $website;
    public $twitter;

    public function __construct(array $data)
    {
        $this->avatar = $data['avatar'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->website = $data['website'] ?? null;
        $this->twitter = $data['twitter'] ?? null;
    }
}
