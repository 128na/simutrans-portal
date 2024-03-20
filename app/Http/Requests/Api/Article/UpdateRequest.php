<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Article;

use App\Constants\NgWords;
use App\Rules\NgWordRule;
use App\Rules\NotJustNumbers;
use App\Rules\UniqueSlugByUser;
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
            'article.title' => ['required', 'max:255', 'unique:articles,title,'.$articleId, new NgWordRule(NgWords::ARTICLE_TITLE)],
            'article.slug' => ['required', 'max:255', new NotJustNumbers, new UniqueSlugByUser],
            'article.articles' => 'present|array|max:10',
            'article.articles.*.id' => 'required|distinct|exists:articles,id,status,publish',
            'should_notify' => 'nullable|boolean',
            'without_update_modified_at' => 'nullable|boolean',
        ];
    }
}
