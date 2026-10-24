<?php

declare(strict_types=1);

namespace App\Http\Requests\Article;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class ConvertToPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $article = $this->route('article');

        return $article instanceof Article && $this->user()?->id === $article->user_id;
    }

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return ['file' => ['required', 'integer', 'min:1']];
    }
}
