<?php

namespace App\Http\Requests\Api\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (request()->has('only_image')) {
            return [
                'file' => 'required|file|image',
            ];
        }
        return [
            'file' => 'required|file',
        ];
    }
}
