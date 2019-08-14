<?php

namespace App\Models\Contents;

class AddonPostContent extends Content
{
    public $description, $file, $author, $license, $thanks, $agreement;

    public function __construct($content)
    {
        parent::__construct($content);

        $this->description = $this->content['description'] ?? null;
        $this->file = $this->content['file'] ?? null;
        $this->author = $this->content['author'] ?? null;
        $this->license = $this->content['license'] ?? null;
        $this->thanks = $this->content['thanks'] ?? null;
        $this->agreement = $this->content['agreement'] ?? false;
    }
}
