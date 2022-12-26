<?php

namespace App\Http\Controllers\Api\Front;

use App\Events\ArticleConversion;
use App\Events\ArticleShown;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Auth;

class ConversionController extends Controller
{
    public function __construct(private Dispatcher $dispatcher)
    {
    }

    public function conversion(Article $article): void
    {
        abort_unless($article->is_publish, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            $this->dispatcher->dispatch(new ArticleConversion($article));
        }
    }

    public function shown(Article $article): void
    {
        abort_unless($article->is_publish, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            $this->dispatcher->dispatch(new ArticleShown($article));
        }
    }
}
