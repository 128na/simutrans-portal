<?php

namespace App\Http\Requests\Api\Article;

use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    protected function baseRule()
    {
        return [
            'article.post_type' => ['required', Rule::in(config('post_types'))],
            'article.status' => ['required', Rule::in(config('status'))],
            'article.title' => 'required|unique:articles,title|max:255',
            'article.slug' => 'required|unique:articles,slug|max:255',
            'article.should_tweet' => 'nullable',
            'preview' => 'nullable',
        ];
    }
}
