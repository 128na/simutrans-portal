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
                'latest' => Article::addon()->active()->withForList()->limit(3)->get(),
                'random' => Article::addon()->active()->withForList()->withoutGlobalScope('order')->inRandomOrder()->limit(3)->get(),
            ]
        ];

        return static::viewWithHeader('front.index', $data);
    }

}
