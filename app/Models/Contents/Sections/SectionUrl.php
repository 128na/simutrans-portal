<?php

declare(strict_types=1);

namespace App\Models\Contents\Sections;

class SectionUrl extends Section
{
    public ?string $url;

    public function __construct(array $section)
    {
        parent::__construct($section);
        $this->url = $section['url'] ?? null;
    }
}
