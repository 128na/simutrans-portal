<?php

namespace App\Models\Contents;

abstract class Content
{
    public $thumbnail;

    public function __construct(array $contents)
    {
        $this->thumbnail = $contents['thumbnail'] ?? null;
    }

    abstract public function getDescription();
}
