<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\InviteRequest;
use App\Http\Resources\Api\Mypage\Invites;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Models\User;
use App\Notifications\UserInvited;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class InvitationCodeController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * 招待したユーザー一覧.
     */
    public function index(): Invites
    {
        $invites = $this->userRepository->getInvites($this->loggedinUser());

        return new Invites($invites);
    }

    /**
     * 招待コード生成.
     */
    public function update(): UserResouce
    {
        $this->userRepository->update($this->loggedinUser(), ['invitation_code' => Str::uuid()]);

        return new UserResouce($this->loggedinUser()->fresh());
    }

    /**
     * 招待コード削除.
     */
    public function destroy(): UserResouce
    {
        $this->userRepository->update($this->loggedinUser(), ['invitation_code' => null]);

        return new UserResouce($this->loggedinUser()->fresh());
    }

    public function register(User $user, InviteRequest $request): UserResouce
    {
        /**
         * @var User $invitedUser
         */
        $invitedUser = $this->userRepository->store([
            'name' => $request->name,
            'email' => $request->email,
            'role' => config('role.user'),
            'password' => Hash::make($request->password),
            'invited_by' => $user->id,
        ]);
        // なぜかオブザーバーが発火しない
        $invitedUser->syncRelatedData();

        Auth::login($invitedUser);
        Session::regenerate();

        event(new Registered($invitedUser));
        $user->notify(new UserInvited($invitedUser));

        return new UserResouce($invitedUser);
    }
}
