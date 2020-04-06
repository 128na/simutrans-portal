<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $user_service;
    //
    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }
    public function index()
    {
        $user = Auth::user()->load('profile');

        return new UserResouce($user);
    }
    public function update(UpdateRequest $request)
    {
        $user = $this->user_service->updateUserAndProfile(Auth::user(), $request);

        return new UserResouce($user);
    }
}
