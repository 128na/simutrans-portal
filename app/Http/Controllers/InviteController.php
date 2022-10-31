<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;

class InviteController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
        abort_unless(config('app.enable_invite'), 400, '招待機能は現在使用できません');
    }

    public function index(User $user)
    {
        return view('front.spa');
    }
}
