<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Article;

use App\Constants\NgWords;
use App\Rules\NgWordRule;
use App\Rules\NotJustNumbers;
use App\Rules\UniqueSlugByUser;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    /**
     * @return array<mixed>
     */
    protected function baseRule(): array
    {
        return [
            'article.post_type' => ['bail', 'required', Rule::in(config('post_types'))],
            'article.status' => ['required', Rule::in(config('status'))],
            'article.title' => ['required', 'max:255', 'unique:articles,title', new NgWordRule(NgWords::ARTICLE_TITLE)],
            'article.slug' => ['required', 'max:255', new NotJustNumbers, new UniqueSlugByUser],
            'article.published_at' => 'nullable|date|after:+1 hour',
            'should_notify' => 'nullable|boolean',
        ];
    }
}
