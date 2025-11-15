<?php

declare(strict_types=1);

namespace App\Models\Contents;

final class AddonIntroductionContent extends Content
{
    public null|string $description;

    public null|string $link;

    public null|string $author;

    public null|string $license;

    public null|string $thanks;

    public null|bool $agreement;

    public null|bool $exclude_link_check;

    /**
     * @param  array{description?:string,author?:string,license?:string,thanks?:string,link?:string,agreement?:bool,exclude_link_check?:bool}  $contents
     */
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

    #[\Override]
    public function getDescription(): string
    {
        return $this->description ?? '';
    }
}
