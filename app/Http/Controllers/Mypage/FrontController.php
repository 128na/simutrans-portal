<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('articles');

        return view('mypage.index', compact('user'));
    }
}
