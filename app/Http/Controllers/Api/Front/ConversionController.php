<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

final class ConversionController extends Controller
{
    public function __construct() {}

    public function conversion(Article $article): void
    {
        abort_unless($article->is_publish, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new \App\Events\ArticleConversion($article));
        }
    }

    public function shown(Article $article): void
    {
        abort_unless($article->is_publish, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new \App\Events\ArticleShown($article));
        }
    }
}
