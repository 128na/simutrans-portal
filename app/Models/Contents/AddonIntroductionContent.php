<?php

namespace App\Models\Contents;

class AddonIntroductionContent extends Content
{
    public $thumbnail;
    public $description;
    public $link;
    public $author;
    public $license;
    public $thanks;
    public $agreement;
    public $exclude_link_check;

    public function __construct(array $contents)
    {
        $this->thumbnail = $contents['thumbnail'] ?? null;
        $this->description = $contents['description'] ?? null;
        $this->link = $contents['link'] ?? null;
        $this->author = $contents['author'] ?? null;
        $this->license = $contents['license'] ?? null;
        $this->thanks = $contents['thanks'] ?? null;
        $this->agreement = $contents['agreement'] ?? false;
        $this->exclude_link_check = $contents['exclude_link_check'] ?? false;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
