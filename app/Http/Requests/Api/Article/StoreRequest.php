<?php

namespace App\Http\Requests\Api\Article;

use App\Rules\NgWordRule;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    protected function baseRule()
    {
        return [
            'article.post_type' => ['bail', 'required', Rule::in(config('post_types'))],
            'article.status' => ['required', Rule::in(config('status'))],
            'article.title' => ['required', 'unique:articles,title', 'max:255', new NgWordRule(['#', '@'])],
            'article.slug' => 'required|unique:articles,slug|max:255',
            'should_tweet' => 'nullable',
            'preview' => 'nullable',
        ];
    }
}
