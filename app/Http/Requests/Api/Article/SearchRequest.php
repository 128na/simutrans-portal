<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Article;

use Illuminate\Foundation\Http\FormRequest;

final class SearchRequest extends FormRequest
{
    /**
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'word' => 'nullable|string|max:100',
            'order' => 'nullable|in:published_at,modified_at',
        ];
    }
}
