<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Http\Requests\User\UpdateRequest;
use App\Models\User;
use App\Repositories\AttachmentRepository;
use App\Repositories\User\ProfileRepository;
use App\Repositories\UserRepository;

final readonly class UpdateProfile
{
    public function __construct(
        private UserRepository $userRepository,
        private ProfileRepository $profileRepository,
        private AttachmentRepository $attachmentRepository,
    ) {}

    public function __invoke(User $user, UpdateRequest $updateRequest): User
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
                'data' => [
                    'avatar' => $updateRequest->input('user.profile.data.avatar'),
                    'description' => $updateRequest->input('user.profile.data.description'),
                    'website' => array_values(array_filter($updateRequest->input('user.profile.data.website'))),
                ],
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
