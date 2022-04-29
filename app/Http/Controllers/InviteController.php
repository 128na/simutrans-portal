<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\InviteRequest;
use App\Models\User;
use App\Notifications\UserInvited;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class InviteController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function index(User $user)
    {
        return view('front.invite.index', ['user' => $user]);
    }

    public function store(User $user, InviteRequest $request)
    {
        $invitedUser = $this->userRepository->store([
            'name' => $request->name,
            'email' => $request->email,
            'role' => config('role.user'),
            'password' => Hash::make($request->password),
            'invited_by' => $user->id,
        ]);

        Auth::login($invitedUser);
        Session::regenerate();

        event(new Registered($invitedUser));
        $user->notify(new UserInvited($invitedUser));

        return redirect()->route('mypage.index');
    }
}
