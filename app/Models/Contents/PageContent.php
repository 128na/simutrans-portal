<?php

namespace App\Models\Contents;

class PageContent extends Content
{
    public $sections;

    public function __construct($content)
    {
        parent::__construct($content);

        $this->sections = $this->content['sections'] ?? [];
    }
}
