<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $data = [
            'articles' => [
                'latest' => Article::active()->with('user', 'attachments', 'categories')->latest()->limit(5)->get(),
                'random' => Article::active()->with('user', 'attachments', 'categories')->withoutGlobalScope('order')->inRandomOrder()->limit(5)->get(),
            ]
        ];

        return static::viewWithHeader('front.index', $data);
    }

    public function articles(Article $article)
    {
        abort_unless($article->is_publish, 404);

        $data = [
            'article' => $article->load('user', 'attachments', 'categories'),
        ];
        return static::viewWithHeader('front.articles', $data);
    }
    public function download(Article $article)
    {
        abort_unless($article->is_publish, 404);

        $article->load('attachments');
        abort_unless($article->has_file, 404);

        return response()
            ->download(public_path('storage/'.$article->file->path), $article->file->original_name);
    }
}
