<?php

namespace App\Models\Contents\Sections;

class SectionImage extends Section
{
    public ?string $id;

    public function __construct(array $section)
    {
        parent::__construct($section);
        $this->id = $section['id'] ?? null;
    }
}
