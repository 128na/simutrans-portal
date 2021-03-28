<?php

namespace App\Services;

use cebe\markdown\GithubMarkdown;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\Blade;

/**
 * @see https://github.com/cebe/markdown
 */
class MarkdownService extends Service
{
    private GithubMarkdown $parser;
    private HTMLPurifier $purifier;

    public function __construct(GithubMarkdown $parser)
    {
        $this->parser = $parser;
        $this->parser->html5 = true;
        $this->parser->enableNewlines = true;

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.AllowedElements', [
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'hr',
            'pre', 'code',
            'blockquote',
            'table', 'tr', 'td', 'th', 'thead', 'tbody',
            'strong', 'em', 'b', 'i', 'u', 's', 'span',
            'a', 'p', 'br',
            'ul', 'ol', 'li',
            'img',
        ]);
        $this->purifier = new HTMLPurifier($config);
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
