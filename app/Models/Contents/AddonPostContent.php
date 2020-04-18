<?php

namespace App\Models\Contents;

class AddonPostContent extends Content
{
    public $thumbnail;
    public $description;
    public $file;
    public $author;
    public $license;
    public $thanks;

    public function __construct(array $contents)
    {
        $this->thumbnail = $contents['thumbnail'] ?? null;
        $this->description = $contents['description'] ?? null;
        $this->file = $contents['file'] ?? null;
        $this->author = $contents['author'] ?? null;
        $this->license = $contents['license'] ?? null;
        $this->thanks = $contents['thanks'] ?? null;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
