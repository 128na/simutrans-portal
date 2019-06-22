<?php

namespace App\Http\Controllers\Mypage;

use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        // avatar
        if ($request->hasFile('avatar')) {
            $avatar = self::saveAttachment($request->file('avatar'), $user->id,
                $user->profile, $user->profile->getContents('avatar') ?? null);
            $user->profile->setContents('avatar', $avatar->id);
            $user->profile->attachments()->save($avatar);
        }

        $user->profile->setContents('description', $request->input('description'));
        $user->profile->setContents('website', $request->input('website'));
        $user->profile->setContents('twitter', $request->input('twitter'));
        $user->profile->save();

        if ($email_changed) {
            $user->sendEmailVerificationNotification();
        }

        session()->flash('success', __('user.updated'));
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
            'thumbnail'   => 'nullable|image',
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
