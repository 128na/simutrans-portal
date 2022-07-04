<?php

namespace App\Http\Requests\Api\Article;

use App\Models\User;
use App\Rules\NgWordRule;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    protected function baseRule()
    {
        return [
            'article.post_type' => ['bail', 'required', Rule::in(config('post_types'))],
            'article.status' => ['required', Rule::in(config('status'))],
            'article.title' => ['required', 'unique:articles,title', 'max:255', new NgWordRule(User::TITLE_NG_WORDS)],
            'article.slug' => 'required|unique:articles,slug|max:255',
            'article.published_at' => 'nullable|date|after:+1 hour',
            'should_tweet' => 'nullable|boolean',
            'preview' => 'nullable',
        ];
    }
}
