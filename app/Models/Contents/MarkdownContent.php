<?php

declare(strict_types=1);

namespace App\Models\Contents;

use App\Services\MarkdownService;

final class MarkdownContent extends Content
{
    public ?string $markdown;

    /**
     * @param  array{markdown?:string}  $contents
     */
    public function __construct(array $contents)
    {
        $this->markdown = $contents['markdown'] ?? null;
        parent::__construct($contents);
    }

    #[\Override]
    public function getDescription(): string
    {
        return app(MarkdownService::class)->toEscapedText($this->markdown ?? '');
    }
}
