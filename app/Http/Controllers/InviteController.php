<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;

class InviteController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function index(User $user)
    {
        return view('front.spa');
    }
}
