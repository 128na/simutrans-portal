<?php

declare(strict_types=1);

namespace App\Models\Contents\Sections;

abstract class Section
{
    public null|string $type;

    /**
     * @param  array<mixed>  $section
     */
    public function __construct(array $section)
    {
        $this->type = $section['type'] ?? null;
    }
}
