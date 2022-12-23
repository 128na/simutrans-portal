<?php

namespace App\Models\Contents;

use App\Services\MarkdownService;

class MarkdownContent extends Content
{
    public $markdown;

    public function __construct(array $contents)
    {
        $this->markdown = $contents['markdown'] ?? null;
        parent::__construct($contents);
    }

    public function getDescription()
    {
        return app(MarkdownService::class)->toEscapedHTML($this->markdown);
    }
}
