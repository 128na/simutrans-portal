<?php

namespace App\Http\Controllers;

class MypageController extends Controller
{
    public function index()
    {
        return view('mypage');
    }

    /**
     * SPA用にマイページトップへリダイレクトさせる.
     */
    public function fallback()
    {
        return redirect()->route('mypage.index');
    }
}
