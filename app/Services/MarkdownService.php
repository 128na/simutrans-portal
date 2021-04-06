<?php

namespace App\Services;

use cebe\markdown\GithubMarkdown;
use HTMLPurifier;
use Illuminate\Support\Facades\Blade;

/**
 * @see https://github.com/cebe/markdown
 */
class MarkdownService extends Service
{
    private GithubMarkdown $parser;
    private HTMLPurifier $purifier;

    public function __construct(GithubMarkdown $parser, HTMLPurifier $purifier)
    {
        $this->parser = $parser;
        $this->parser->html5 = true;
        $this->parser->enableNewlines = true;

        $this->purifier = $purifier;
    }

    public function toEscapedHTML(string $markdown)
    {
        $raw = $this->parser->parse($markdown);

        return $this->purifier->purify($raw);
    }

    public static function registerBlade($directive = 'markdown')
    {
        Blade::directive($directive, function ($expression) {
            return "<?php echo app('\App\Services\MarkdownService')->toEscapedHTML($expression); ?>";
        });
    }
}
