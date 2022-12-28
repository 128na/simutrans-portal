<?php

namespace App\Models\Contents\Sections;

class Section
{
    public ?string $type;

    /**
     * @param  array<mixed>  $section
     */
    public function __construct(array $section)
    {
        $this->type = $section['type'] ?? null;
    }
}
