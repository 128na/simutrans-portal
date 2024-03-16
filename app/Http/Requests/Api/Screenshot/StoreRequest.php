<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Screenshot;

use App\Enums\ScreenShotStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:256',
            'description' => 'required|string|max:1024',
            'links' => 'required|array',
            'links.*' => 'url',
            'status' => ['required', Rule::in(ScreenShotStatus::cases())],
            'attachments' => 'required|array',
            'attachments.*' => 'required|exists:attachments,id',
            'articles' => 'required|array',
            'articles.*' => 'required|exists:articles,id',
        ];
    }
}
