<?php
namespace App\Services;

use App\Http\Requests\Api\User\UpdateRequest;
use App\Models\Profile;
use App\Models\User;

class UserService extends Service
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getUser(User $user)
    {
        return $user->load('profile');
    }

    public function updateUserAndProfile(User $user, UpdateRequest $request)
    {
        $email_changed = $user->email !== $request->input('user.email');

        $user->update([
            'name' => $request->input('user.name'),
            'email' => $request->input('user.email'),
            'email_verified_at' => $email_changed
            ? null
            : $user->email_verified_at,
        ]);

        $this->updateProfile($user->profile, $request);
        $this->syncRelated($user, $request);

        if ($email_changed) {
            $user->sendEmailVerificationNotification();
        }

        return $user->refresh();
    }

    private function updateProfile(Profile $profile, UpdateRequest $request)
    {
        $profile->update([
            'data' => $request->input('user.profile.data'),
        ]);
    }

    private function syncRelated(User $user, UpdateRequest $request)
    {
        if ($request->filled('user.profile.data.avatar')) {
            $user->profile->attachments()->save(
                $user->myAttachments()->find($request->input('user.profile.data.avatar'))
            );
        }
    }

}
