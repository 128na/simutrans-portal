<?php

declare(strict_types=1);

namespace App\Models\Contents;

final class AddonPostContent extends Content
{
    public null|string $description;

    public null|int $file;

    public null|string $author;

    public null|string $license;

    public null|string $thanks;

    /**
     * @param  array{description?:string,file?:int,author?:string,license?:string,thanks?:string}  $contents
     */
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

    #[\Override]
    public function getDescription(): string
    {
        return $this->description ?? '';
    }
}
