<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->input('token', null);

        abort_unless($token === config('auth.simutrans_search_token', false), 403);

        $articles = Article::active()->get();

        return view('simutrans_search.index', compact('articles'));
    }
}
