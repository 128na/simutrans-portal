<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return view('front.mypage');
    }
}
