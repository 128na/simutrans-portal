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
        if (request()->input('only_image')) {
            return [
                'file' => 'required|file|image',
                'only_image' => 'nullable',
            ];
        }

        return [
            'file' => 'required|file',
            'only_image' => 'nullable',
        ];
    }
}
