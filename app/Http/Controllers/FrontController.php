<?php

namespace App\Http\Controllers;

use App\Models\Article;

/**
 * トップページ
 */
class FrontController extends Controller
{
    public function index()
    {
        $data = [
            'articles' => [
                'latest' => Article::active()->withForList()->limit(3)->get(),
                'random' => Article::active()->withForList()->withoutGlobalScope('order')->inRandomOrder()->limit(3)->get(),
            ]
        ];

        return static::viewWithHeader('front.index', $data);
    }

}
