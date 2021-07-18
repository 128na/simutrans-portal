<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\Loggedin;
use Auth;
use Illuminate\Http\Request;

class OauthController extends Controller
{
    public function showLogin()
    {
        return view('auth.oauth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            Auth::user()->notify(new Loggedin());

            return redirect()->intended();
        }

        return back()->withErrors([
            'email' => 'ログインできませんでした。',
        ]);
    }
}
