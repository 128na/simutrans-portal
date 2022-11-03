<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class InviteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:11',
        ];
    }
}
