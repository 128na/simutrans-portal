<?php

declare(strict_types=1);

namespace App\Models\Contents;

class AddonIntroductionContent extends Content
{
    public ?string $description;

    public ?string $link;

    public ?string $link_type;

    public ?string $author;

    public ?string $license;

    public ?string $thanks;

    public ?bool $agreement;

    public ?bool $exclude_link_check;

    /**
     * @param  array{description?:string,author?:string,license?:string,thanks?:string,link?:string,link_type?:string|null,agreement?:bool,exclude_link_check?:bool}  $contents
     */
    public function __construct(array $contents)
    {
        $this->description = $contents['description'] ?? null;
        $this->link = $contents['link'] ?? null;
        $this->link_type = $contents['link_type'] ?? null;
        $this->author = $contents['author'] ?? null;
        $this->license = $contents['license'] ?? null;
        $this->thanks = $contents['thanks'] ?? null;
        $this->agreement = (bool) ($contents['agreement'] ?? false);
        $this->exclude_link_check = (bool) ($contents['exclude_link_check'] ?? false);
        parent::__construct($contents);
    }

    #[\Override]
    public function getDescription(): string
    {
        return $this->description ?? '';
    }
}
