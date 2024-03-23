<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Enums\ArticleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleUpdateRequest extends FormRequest
{
    /**
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'article.status' => ['nullable', Rule::enum(ArticleStatus::class)],
        ];
    }
}
