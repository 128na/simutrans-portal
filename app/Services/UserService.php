<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Requests\Api\User\UpdateRequest;
use App\Models\User;
use App\Repositories\AttachmentRepository;
use App\Repositories\User\ProfileRepository;
use App\Repositories\UserRepository;

class UserService
{
    public function __construct(private readonly UserRepository $userRepository, private readonly ProfileRepository $profileRepository, private readonly AttachmentRepository $attachmentRepository)
    {
    }

    public function getUser(User $user): User
    {
        return $this->userRepository->load($user, ['profile:id,user_id,data']);
    }

    public function updateUserAndProfile(User $user, UpdateRequest $updateRequest): User
    {
        $emailChanged = $user->email !== $updateRequest->input('user.email');

        $this->userRepository->update($user, [
            'name' => $updateRequest->input('user.name'),
            'nickname' => $updateRequest->input('user.nickname'),
            'email' => $updateRequest->input('user.email'),
            'email_verified_at' => $emailChanged
                ? null
                : $user->email_verified_at,
        ]);

        if ($user->profile) {
            $this->profileRepository->update($user->profile, [
                'data' => $updateRequest->input('user.profile.data'),
            ]);
        }

        if ($updateRequest->filled('user.profile.data.avatar')) {
            $this->attachmentRepository->syncProfile($user, $updateRequest->integer('user.profile.data.avatar'));
        }

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return $user->refresh();
    }
}
