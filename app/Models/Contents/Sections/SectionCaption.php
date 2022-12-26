<?php

namespace App\Models\Contents\Sections;

class SectionCaption extends Section
{
    public ?string $caption;

    public function __construct(array $section)
    {
        parent::__construct($section);
        $this->caption = $section['caption'] ?? null;
    }
}
