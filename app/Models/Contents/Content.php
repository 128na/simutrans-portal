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
        $this->thumbnail = array_key_exists('thumbnail', $contents) ? (int) $contents['thumbnail'] : null;
    }

    abstract public function getDescription(): string;
}
