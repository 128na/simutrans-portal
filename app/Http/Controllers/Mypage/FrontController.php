<?php

namespace App\Http\Controllers\Mypage;

use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('profile');
        $articles = Article::where('user_id', $user->id)
            ->with('categories', 'todaysViewCount', 'todaysConversionCount')->get();

        return view('mypage.index', compact('user', 'articles'));
    }
}
