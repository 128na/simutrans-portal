<?php

namespace App\Models\Contents\Sections;

class SectionText extends Section
{
    public ?string $text;

    public function __construct(array $section)
    {
        parent::__construct($section);
        $this->text = $section['text'] ?? null;
    }
}
