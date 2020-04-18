<?php

namespace App\Models\Contents\Sections;

class SectionImage extends Section
{
    public $id;

    public function __construct($section)
    {
        parent::__construct($section);
        $this->id = $section['id'] ?? null;
    }
}
