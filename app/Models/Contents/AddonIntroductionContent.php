<?php

namespace App\Models\Contents;

class AddonIntroductionContent extends Content
{
    public $description, $link, $author, $license, $thanks, $agreement;

    public function __construct($content)
    {
        parent::__construct($content);

        $this->description = $this->content['description'] ?? null;
        $this->link = $this->content['link'] ?? null;
        $this->author = $this->content['author'] ?? null;
        $this->license = $this->content['license'] ?? null;
        $this->thanks = $this->content['thanks'] ?? null;
        $this->agreement = $this->content['agreement'] ?? false;
    }
}
