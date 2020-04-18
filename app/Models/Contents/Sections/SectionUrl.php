<?php

namespace App\Models\Contents\Sections;

class SectionUrl extends Section
{
    public $url;

    public function __construct($section)
    {
        parent::__construct($section);
        $this->url = $section['url'] ?? null;
    }
}
