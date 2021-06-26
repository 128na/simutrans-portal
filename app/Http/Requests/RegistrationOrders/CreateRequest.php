<?php

namespace App\Http\Requests\RegistrationOrders;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|unique:users|unique:registration_orders',
            'twitter' => 'required|max:255',
            'code' => 'nullable|max:255',
        ];
    }
}
