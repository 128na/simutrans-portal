<?php

declare(strict_types=1);

namespace App\Services;

use cebe\markdown\GithubMarkdown;
use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * @see https://github.com/cebe/markdown
 */
final readonly class MarkdownService
{
    public function __construct(
        private GithubMarkdown $githubMarkdown,
        private HTMLPurifier $htmlPurifier,
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
            function (array $matches): string {
                $attrs = $matches[1];

                if (in_array(preg_match('/target=/', $attrs), [0, false], true)) {
                    $attrs .= ' target="_blank"';
                }

                if (in_array(preg_match('/rel=/', $attrs), [0, false], true)) {
                    $attrs .= ' rel="noopener noreferrer"';
                }

                return sprintf('<a %s>', $attrs);
            },
            $pure,
        );

        return $html ?? '';
    }

    public function toEscapedText(string $markdown): string
    {
        $raw = $this->githubMarkdown->parse($markdown);

        $htmlPurifierConfig = HTMLPurifier_Config::createDefault();
        $htmlPurifierConfig->set('HTML.AllowedElements', []);

        return $this->htmlPurifier->purify($raw, $htmlPurifierConfig);
    }
}
