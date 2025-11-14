<?php

declare(strict_types=1);

namespace App\Models\User;

final class ProfileData
{
    public ?int $avatar;

    public ?string $description;

    /**
     * @var string[]
     */
    public array $website;

    /**
     * @param  array{avatar?:int,description?:string,website?:string}  $data
     */
    public function __construct(array $data)
    {
        $website = $data['website'] ?? null;
        if (is_null($website)) {
            $website = [];
        } elseif (is_string($website)) {
            $website = [$website];
        }

        $id = $data['avatar'] ?? null;
        $this->avatar = $id ? (int) $id : null;
        $this->description = $data['description'] ?? null;
        $this->website = $website;
    }
}
