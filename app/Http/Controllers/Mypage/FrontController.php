<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FrontController extends Controller
{
    public function index()
    {
        return view('mypage.index');
    }
}
