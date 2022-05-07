<?php

namespace App\Http\Requests\Api\User;

use App\Rules\ImageAttachment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user_id = Auth::id();

        return [
            'user.name' => "required|unique:users,name,{$user_id}|max:255",
            'user.email' => "required|email|unique:users,email,{$user_id}|max:255",
            'user.profile' => 'required|array',
            'user.profile.data' => 'required|array',
            'user.profile.data.avatar' => ['nullable', 'exists:attachments,id,user_id,'.Auth::id(), app(ImageAttachment::class)],
            'user.profile.data.description' => 'nullable|max:255',
            'user.profile.data.website' => 'nullable|url|max:255',
            'user.profile.data.twitter' => 'nullable|max:255',
            'user.profile.data.gtag' => 'nullable|starts_with:UA-,G-',
        ];
    }
}
