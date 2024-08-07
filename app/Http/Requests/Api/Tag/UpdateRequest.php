<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tag;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:1024',
        ];
    }
}
