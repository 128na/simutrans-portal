<?php

namespace App\Models\Contents\Sections;

class SectionCaption extends Section
{
    public $caption;

    public function __construct($section)
    {
        parent::__construct($section);
        $this->caption = $section['caption'] ?? null;
    }
}
