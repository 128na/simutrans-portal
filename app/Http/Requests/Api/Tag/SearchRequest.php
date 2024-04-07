<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tag;

use Illuminate\Foundation\Http\FormRequest;

final class SearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
        ];
    }
}
