<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

class MypageController extends Controller
{
    public function index(): Renderable
    {
        return view('mypage');
    }
}
