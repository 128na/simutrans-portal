<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('profile');
        $articles = $user->articles()
            ->with('categories', 'todaysViewCount', 'todaysConversionCount')->get();

        return view('mypage.index', compact('user', 'articles'));
    }
}
