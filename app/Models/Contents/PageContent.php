<?php

namespace App\Models\Contents;

class PageContent extends Content
{
    public PageContentsSections $sections;

    public function __construct(array $contents)
    {
        $this->sections = new PageContentsSections($contents['sections'] ?? []);
        parent::__construct($contents);
    }

    public function getDescription(): string
    {
        return collect($this->sections)->pluck('text')->implode("\n");
    }
}
