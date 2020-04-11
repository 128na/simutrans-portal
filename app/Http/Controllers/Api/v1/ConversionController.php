<?php

namespace App\Http\Controllers\Api\v1;

use App\Events\ArticleConversion;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class ConversionController extends Controller
{
    public function click(Article $article)
    {
        abort_unless($article->is_publish, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleConversion($article));
        }

        return [];
    }
}
