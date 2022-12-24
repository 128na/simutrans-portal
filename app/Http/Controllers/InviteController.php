<?php

namespace App\Http\Controllers;

use App\Models\User;

class InviteController extends Controller
{
    public function __construct()
    {
    }

    public function index(User $user)
    {
        return view('front.spa');
    }
}
