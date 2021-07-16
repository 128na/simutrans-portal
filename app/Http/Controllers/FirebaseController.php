<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Notifications\Loggedin;
use Illuminate\Support\Facades\Route;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Contract\Auth as ContractAuth;
use Kreait\Firebase\Exception\InvalidArgumentException;

class FirebaseController extends Controller
{
    private ContractAuth $auth;

    public function __construct()
    {
        try {
            $provider = Route::current()->parameter('provider');
            $this->auth = Firebase::project($provider)->auth();
        } catch (InvalidArgumentException $e) {
            return response('provider not support.', 400);
        }
    }

    public function redirect(string $provider)
    {
        if (!Auth::check()) {
            return view('firebase.login', ['provider' => $provider]);
        }

        return view('firebase.confirm', ['provider' => $provider]);
    }

    public function login(Request $request, string $provider)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            Auth::user()->notify(new Loggedin());

            return view('firebase.confirm', ['provider' => $provider]);
        }
        return back()->withErrors([
            'email' => 'ログインできませんでした。',
        ]);

    }

    public function accept(string $provider)
    {
        $user = Auth::user();
        try {
            $firebaseUser = $this->auth->getUserByEmail($user->email);
        } catch (UserNotFound $e) {
            $firebaseUser = $this->auth->createUser([
                'email' => $user->email,
                'displayName' => $user->name,
                'emailVerified' => (bool) $user->email_verified_at,
                'disabled' => (bool) $user->deleted_at,
            ]);
        }

        $customToken = $this->auth->createCustomToken($firebaseUser->uid)->toString();

        return redirect("http://localhost:8080/callback?token=$customToken");
    }
}
