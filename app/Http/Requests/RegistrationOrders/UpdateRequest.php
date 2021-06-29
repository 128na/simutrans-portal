<?php

namespace App\Http\Requests\RegistrationOrders;

use App\Models\RegistrationOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => [Rule::in([RegistrationOrder::STATUS_APPROVAL, RegistrationOrder::STATUS_REJECTED])],
        ];
    }
}
