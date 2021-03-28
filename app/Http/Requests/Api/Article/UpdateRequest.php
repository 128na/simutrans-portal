<?php

namespace App\Http\Requests\Api\Article;

use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
{
    protected function baseRule()
    {
        $article_id = request()->input('article.id');

        return [
            'article.status' => ['required', Rule::in(config('status'))],
            'article.title' => "required|unique:articles,title,{$article_id}|max:255",
            'article.slug' => "required|unique:articles,slug,{$article_id}|max:255",
            'should_tweet' => 'nullable',
            'preview' => 'nullable',
        ];
    }
}
