<?php

namespace App\Http\Requests\Api\Article;

use App\Models\User;
use App\Rules\NgWordRule;
use Illuminate\Validation\Rule;

class UpdateRequest extends BaseRequest
{
    /**
     * @return array<mixed>
     */
    protected function baseRule(): array
    {
        $articleId = request()->input('article.id');

        return [
            'article.status' => ['required', Rule::in(config('status'))],
            'article.title' => ['required', "unique:articles,title,{$articleId}", 'max:255', new NgWordRule(User::TITLE_NG_WORDS)],
            'article.slug' => "required|unique:articles,slug,{$articleId}|max:255",
            'should_tweet' => 'nullable|boolean',
            'without_update_modified_at' => 'nullable|boolean',
        ];
    }
}
