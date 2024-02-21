<?php

declare(strict_types=1);

namespace App\Services;

use cebe\markdown\GithubMarkdown;
use HTMLPurifier;

/**
 * @see https://github.com/cebe/markdown
 */
class MarkdownService extends Service
{
    public function __construct(private readonly GithubMarkdown $parser, private readonly HTMLPurifier $purifier)
    {
        $this->parser->html5 = true;
        $this->parser->enableNewlines = true;
    }

    public function toEscapedHTML(string $markdown): string
    {
        $raw = $this->parser->parse($markdown);

        return $this->purifier->purify($raw);
    }

    public function toEscapedAllHTML(string $markdown): string
    {
        $raw = $this->parser->parse($markdown);

        return $this->purifier->purify($raw);
    }
}
