<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Enums\UserRole;
use App\Events\User\InviteCodeCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\InviteRequest;
use App\Http\Resources\Api\Mypage\Invite;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Models\User;
use App\Notifications\UserInvited;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

final class InvitationCodeController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    /**
     * 招待したユーザー一覧.
     */
    public function index(): AnonymousResourceCollection
    {
        $invites = $this->userRepository->findInvites($this->loggedinUser());

        return Invite::collection($invites);
    }

    /**
     * 招待コード生成.
     */
    public function update(): UserResouce
    {
        $this->userRepository->update($this->loggedinUser(), ['invitation_code' => Str::uuid()]);
        InviteCodeCreated::dispatch($this->loggedinUser());

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

    public function register(User $user, InviteRequest $inviteRequest): UserResouce
    {
        /**
         * @var User $model
         */
        $model = $this->userRepository->store([
            'name' => $inviteRequest->name,
            'email' => $inviteRequest->email,
            'role' => UserRole::User,
            'password' => Hash::make((string) $inviteRequest->string('password')),
            'invited_by' => $user->id,
        ]);
        // なぜかオブザーバーが発火しない
        $model->syncRelatedData();

        Auth::login($model);
        Session::regenerate();

        event(new Registered($model));
        $user->notify(new UserInvited($model));

        return new UserResouce($model);
    }
}
