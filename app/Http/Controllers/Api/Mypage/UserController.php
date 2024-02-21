<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Jobs\Article\JobUpdateRelated;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function index(): UserResouce|string
    {
        if (Auth::check()) {
            $user = $this->userService->getUser($this->loggedinUser());

            return new UserResouce($user);
        }

        return '';
    }

    public function update(UpdateRequest $updateRequest): UserResouce
    {
        $user = $this->userService->updateUserAndProfile($this->loggedinUser(), $updateRequest);
        JobUpdateRelated::dispatch();

        return new UserResouce($user);
    }
}
