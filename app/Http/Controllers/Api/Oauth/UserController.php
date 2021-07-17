<?php

namespace App\Http\Controllers\Api\Oauth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show()
    {
        return ['user' => Auth::user()];
    }
}
