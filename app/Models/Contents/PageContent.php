<?php

namespace App\Models\Contents;

class PageContent extends Content
{

    protected $attributes = [
        'thumbnail',
        'sections' => [],
    ];

    public function __construct(array $contents)
    {
        $this->thumbnail = $contents['thumbnail'] ?? null;
        $this->sections = new PageContentsSections($contents['sections'] ?? []);
    }

    public function getDescription()
    {
        return collect($this->sections)->pluck('text')->implode("\n");
    }
}
