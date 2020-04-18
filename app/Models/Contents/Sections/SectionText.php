<?php

namespace App\Models\Contents\Sections;

class SectionText extends Section
{
    public $text;

    public function __construct($section)
    {
        parent::__construct($section);
        $this->text = $section['text'] ?? null;
    }
}
