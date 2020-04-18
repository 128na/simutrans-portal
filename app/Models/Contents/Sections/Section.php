<?php

namespace App\Models\Contents\Sections;

class Section
{
    public $type;

    public function __construct($section)
    {
        $this->type = $section['type'] ?? null;
    }
}
