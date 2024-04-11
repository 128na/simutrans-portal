<?php

declare(strict_types=1);

namespace App\Services;

use cebe\markdown\GithubMarkdown;
use HTMLPurifier;

/**
 * @see https://github.com/cebe/markdown
 */
final readonly class MarkdownService
{
    public function __construct(
        private readonly GithubMarkdown $githubMarkdown,
        private readonly HTMLPurifier $htmlPurifier,
    ) {
        $this->githubMarkdown->html5 = true;
        $this->githubMarkdown->enableNewlines = true;
    }

    public function toEscapedHTML(string $markdown): string
    {
        $raw = $this->githubMarkdown->parse($markdown);

        return $this->htmlPurifier->purify($raw);
    }

    public function toEscapedAllHTML(string $markdown): string
    {
        $raw = $this->githubMarkdown->parse($markdown);

        return $this->htmlPurifier->purify($raw);
    }
}
