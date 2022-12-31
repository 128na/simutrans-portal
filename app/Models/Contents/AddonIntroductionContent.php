<?php

declare(strict_types=1);

namespace App\Models\Contents;

class AddonIntroductionContent extends Content
{
    public ?string $description;

    public ?string $link;

    public ?string $author;

    public ?string $license;

    public ?string $thanks;

    public ?bool $agreement;

    public ?bool $exclude_link_check;

    public function __construct(array $contents)
    {
        $this->description = $contents['description'] ?? null;
        $this->link = $contents['link'] ?? null;
        $this->author = $contents['author'] ?? null;
        $this->license = $contents['license'] ?? null;
        $this->thanks = $contents['thanks'] ?? null;
        $this->agreement = (bool) ($contents['agreement'] ?? false);
        $this->exclude_link_check = (bool) ($contents['exclude_link_check'] ?? false);
        parent::__construct($contents);
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }
}
