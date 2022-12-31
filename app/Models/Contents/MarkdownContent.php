<?php

declare(strict_types=1);

namespace App\Models\Contents;

use App\Services\MarkdownService;

class MarkdownContent extends Content
{
    public ?string $markdown;

    public function __construct(array $contents)
    {
        $this->markdown = $contents['markdown'] ?? null;
        parent::__construct($contents);
    }

    public function getDescription(): string
    {
        return app(MarkdownService::class)->toEscapedHTML($this->markdown ?? '');
    }
}
