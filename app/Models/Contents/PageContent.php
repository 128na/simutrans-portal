<?php

namespace App\Models\Contents;

class PageContent extends Content
{

    protected $attributes = [
        'thumbnail',
        'sections' => [],
    ];

    public function getDescription()
    {
        return collect($this->sections)->pluck('text')->implode("\n");
    }
}
