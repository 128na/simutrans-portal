<?php

declare(strict_types=1);

namespace App\Models\Contents;

abstract class Content
{
    public ?int $thumbnail;

    /**
     * @param  array<mixed>  $contents
     */
    public function __construct(array $contents)
    {
        $id = $contents['thumbnail'] ?? null;
        $this->thumbnail = $id ? (int) $id : null;
    }

    abstract public function getDescription(): string;
}
