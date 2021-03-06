<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $user = $this->userService->getUser(Auth::user());

        return new UserResouce($user);
    }

    public function update(UpdateRequest $request)
    {
        $user = $this->userService->updateUserAndProfile(Auth::user(), $request);

        return new UserResouce($user);
    }
}
