<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mypage\Invite;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Repositories\UserRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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
        event(new \App\Events\User\InviteCodeCreated($this->loggedinUser()));

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
}
