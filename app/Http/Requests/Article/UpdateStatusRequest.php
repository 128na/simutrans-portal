<?php

declare(strict_types=1);

namespace App\Http\Requests\Article;

use App\Enums\ArticleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in([
                ArticleStatus::Publish->value,
                ArticleStatus::Draft->value,
                ArticleStatus::Private->value,
                ArticleStatus::Trash->value,
            ])],
        ];
    }
}
