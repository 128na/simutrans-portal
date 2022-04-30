<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mypage\Invites;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InvitationCodeController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * 招待したユーザー一覧.
     */
    public function index()
    {
        $invites = $this->userRepository->getInvites(Auth::user());

        return new Invites($invites);
    }

    /**
     * 招待コード生成.
     */
    public function update()
    {
        $this->userRepository->update(Auth::user(), ['invitation_code' => Str::uuid()]);

        return new UserResouce(Auth::user()->fresh());
    }

    /**
     * 招待コード削除.
     */
    public function destroy()
    {
        $this->userRepository->update(Auth::user(), ['invitation_code' => null]);

        return new UserResouce(Auth::user()->fresh());
    }
}
