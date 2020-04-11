<?php
namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Blade;
use \cebe\markdown\GithubMarkdown;

/**
 * @see https://github.com/cebe/markdown
 */
class MarkdownService extends Service
{
    /**
     * @var GithubMarkdown
     */
    private $parser;

    public function __construct(Article $model, GithubMarkdown $parser)
    {
        $this->model = $model;
        $this->parser = $parser;
    }

    public function toHTML(string $markdown)
    {
        return $this->parser->parse($markdown);
    }

    public static function registerBlade($directive = 'markdown')
    {
        Blade::directive($directive, function ($expression) {
            return "<?php echo app('\App\Services\MarkdownService')->toHTML($expression); ?>";
        });
    }
}
