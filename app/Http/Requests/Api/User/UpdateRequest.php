<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Constants\NgWords;
use App\Rules\ImageAttachment;
use App\Rules\NgWordRule;
use App\Rules\NotJustNumbers;
use App\Rules\SluggableString;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'user.name' => ['required', 'unique:users,name,'.$userId, 'max:255', new NgWordRule(NgWords::USER_NAME)],
            'user.nickname' => ['nullable', 'unique:users,nickname,'.$userId, 'max:20', new NotJustNumbers, new SluggableString],
            'user.email' => sprintf('required|email|unique:users,email,%s|max:255', $userId),
            'user.profile' => 'required|array',
            'user.profile.data' => 'required|array',
            'user.profile.data.avatar' => ['nullable', 'exists:attachments,id,user_id,'.$userId, app(ImageAttachment::class)],
            'user.profile.data.description' => 'nullable|max:1024',
            'user.profile.data.website' => 'nullable|url|max:255',
        ];
    }
}
