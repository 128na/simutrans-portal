<?php

declare(strict_types=1);

namespace App\Models\Contents;

class AddonPostContent extends Content
{
    public ?string $description;

    public ?int $file;

    public ?string $author;

    public ?string $license;

    public ?string $thanks;

    public function __construct(array $contents)
    {
        $this->description = $contents['description'] ?? null;
        $id = $contents['file'] ?? null;
        $this->file = $id ? (int) $id : null;
        $this->author = $contents['author'] ?? null;
        $this->license = $contents['license'] ?? null;
        $this->thanks = $contents['thanks'] ?? null;
        parent::__construct($contents);
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }
}
