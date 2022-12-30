<?php

declare(strict_types=1);

namespace App\Models\Contents\Sections;

class SectionImage extends Section
{
    public ?int $id;

    public function __construct(array $section)
    {
        parent::__construct($section);
        $this->id = array_key_exists('id', $section) ? (int) $section['id'] : null;
    }
}
