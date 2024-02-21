<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\Api\User\UpdateRequest;
use App\Models\User;
use App\Repositories\AttachmentRepository;
use App\Repositories\User\ProfileRepository;
use App\Repositories\UserRepository;

class UserService extends Service
{
    public function __construct(private readonly UserRepository $userRepository, private readonly ProfileRepository $profileRepository, private readonly AttachmentRepository $attachmentRepository)
    {
    }

    public function getUser(User $user): User
    {
        return $this->userRepository->load($user, ['profile:id,user_id,data']);
    }

    public function updateUserAndProfile(User $user, UpdateRequest $request): User
    {
        $emailChanged = $user->email !== $request->input('user.email');

        $this->userRepository->update($user, [
            'name' => $request->input('user.name'),
            'nickname' => $request->input('user.nickname'),
            'email' => $request->input('user.email'),
            'email_verified_at' => $emailChanged
                ? null
                : $user->email_verified_at,
        ]);

        if ($user->profile) {
            $this->profileRepository->update($user->profile, [
                'data' => $request->input('user.profile.data'),
            ]);
        }

        if ($request->filled('user.profile.data.avatar')) {
            $this->attachmentRepository->syncProfile($user, $request->input('user.profile.data.avatar'));
        }

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return $user->refresh();
    }
}
