<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Article;

use Illuminate\Foundation\Http\FormRequest;

final class ListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'order' => 'nullable|in:published_at,modified_at',
        ];
    }
}
