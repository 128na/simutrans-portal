<?php

namespace App\Http\Controllers;

use App\Models\Article;

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

}
