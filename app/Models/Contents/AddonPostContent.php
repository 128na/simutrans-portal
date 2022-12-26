<?php

namespace App\Models\Contents;

class AddonPostContent extends Content
{
    public ?string $description;

    public ?string $file;

    public ?string $author;

    public ?string $license;

    public ?string $thanks;

    public function __construct(array $contents)
    {
        $this->description = $contents['description'] ?? null;
        $this->file = $contents['file'] ?? null;
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
