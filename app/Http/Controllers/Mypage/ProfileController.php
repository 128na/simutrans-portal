<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load('profile', 'profile.attachments');
        return view('mypage.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        self::validateData($request->all(), static::getValidateRule($user));

        $user->load('profile', 'profile.attachments');

        $email_changed = $user->email !== $request->input('email');

        $user->fill([
            'name'   => $request->input('name'),
            'email'   => $request->input('email'),
            'email_verified_at' => $email_changed
                ? null
                : $user->email_verified_at,
        ]);
        $user->save();

        $profile_data = $user->profile->data;

        $profile_data->avatar = $request->input('avatar_id');
        if ($request->filled('avatar_id')) {
            $user->profile->attachments()->save(Attachment::findOrFail($request->input('avatar_id')));
        }
        $profile_data->description = $request->input('description');
        $profile_data->website = $request->input('website');
        $profile_data->twitter = $request->input('twitter');

        $user->profile->data = $profile_data;
        $user->profile->save();

        if ($email_changed) {
            $user->sendEmailVerificationNotification();
        }

        session()->flash('success', __('Profile Updated.'));
        return redirect()->route('mypage.index');
    }

    /**
     * 入力値のバリデーション
     */
    private static function validateData($data, $rule)
    {
        return Validator::make($data, $rule)->validate();
    }

    /**
     * バリデーションルールを返す
     */
    protected static function getValidateRule($user)
    {
        return [
            'name'        => "required|max:255",
            'email'       => "required|email|unique:users,email,{$user->id}max:255",
            'avatar_id'   => 'nullable|exists:attachments,id',
            'description' => 'nullable|max:255',
            'website'     => 'nullable|url|max:255',
            'twitter'     => 'nullable|max:255',
        ];
    }

    /**
     * 添付ファイルの保存
     */
    protected static function saveAttachment($file, $user_id, $attachmentable, $old_attachment_id = null)
    {
        $new_attachment = Attachment::createFromFile($file, $user_id);
        if ($old_attachment_id) {
            Attachment::destroy($old_attachment_id);
        }
        return $new_attachment;
    }
}
