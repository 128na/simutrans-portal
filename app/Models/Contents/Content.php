<?php

declare(strict_types=1);

namespace App\Models\Contents;

abstract class Content
{
    public ?string $thumbnail;

    /**
     * @param  array<mixed>  $contents
     */
    public function __construct(array $contents)
    {
        $this->thumbnail = $contents['thumbnail'] ?? null;
    }

    abstract public function getDescription(): string;
}
