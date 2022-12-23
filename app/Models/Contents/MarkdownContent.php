<?php

namespace App\Models\Contents;

use App\Services\MarkdownService;

class MarkdownContent extends Content
{
    public $thumbnail;

    public $markdown;

    public function __construct(array $contents)
    {
        $this->thumbnail = $contents['thumbnail'] ?? null;
        $this->markdown = $contents['markdown'] ?? null;
    }

    public function getDescription()
    {
        return app(MarkdownService::class)->toEscapedHTML($this->markdown);
    }
}
