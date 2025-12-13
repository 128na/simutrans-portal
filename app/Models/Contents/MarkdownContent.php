<?php

declare(strict_types=1);

namespace App\Models\Contents;

use App\Services\MarkdownService;

class MarkdownContent extends Content
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
        return resolve(MarkdownService::class)->toEscapedText($this->markdown ?? '');
    }
}
