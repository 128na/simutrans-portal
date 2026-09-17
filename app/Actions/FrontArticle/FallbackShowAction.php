<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;

class FallbackShowAction
{
    public function __invoke(int|string $slugOrId): RedirectResponse
    {
        $article = is_numeric($slugOrId)
            ? Article::findOrFail($slugOrId)
            : Article::slug($slugOrId)->orderBy('id', 'asc')->firstOrFail();

        return redirect($article->showUrl(), 302);
    }
}
