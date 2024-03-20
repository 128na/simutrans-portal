<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Attachment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        if (request()->input('only_image')) {
            return [
                'files' => 'required|array|max:10',
                'files.*' => 'required|file|image',
                'only_image' => 'nullable',
                'crop.top' => 'integer|min:0|max:128',
                'crop.bottom' => 'integer|min:0|max:128',
                'crop.left' => 'integer|min:0|max:128',
                'crop.right' => 'integer|min:0|max:128',
            ];
        }

        return [
            'files' => 'required|array|max:10',
            'files.*' => 'required|file',
            'only_image' => 'nullable',
        ];
    }
}
