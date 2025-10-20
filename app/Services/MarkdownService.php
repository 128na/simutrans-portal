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

        $pure = $this->htmlPurifier->purify($raw);
        $html = preg_replace_callback(
            '/<a\s+([^>]+)>/i',
            function ($matches) {
                $attrs = $matches[1];

                if (!preg_match('/target=/', $attrs)) {
                    $attrs .= ' target="_blank"';
                }
                if (!preg_match('/rel=/', $attrs)) {
                    $attrs .= ' rel="noopener noreferrer"';
                }

                return "<a {$attrs}>";
            },
            $pure

        );

        return $html ?? '';
    }
}
