<?php

declare(strict_types=1);

namespace App\Http\Requests\Article;

use App\Constants\NgWords;
use App\Enums\ArticleStatus;
use App\Rules\NgWordRule;
use App\Rules\NotJustNumbers;
use App\Rules\ReservationPublishedAt;
use App\Rules\UniqueSlugByUser;
use Illuminate\Validation\Rule;

final class UpdateRequest extends BaseRequest
{
    /**
     * @return array<mixed>
     */
    #[\Override]
    protected function baseRule(): array
    {
        $articleId = $this->integer('article.id');

        return [
            'article.status' => ['required', Rule::enum(ArticleStatus::class)],
            'article.title' => ['required', 'max:255', 'unique:articles,title,'.$articleId, new NgWordRule(NgWords::ARTICLE_TITLE)],
            'article.slug' => ['required', 'max:255', new NotJustNumbers, new UniqueSlugByUser],
            'article.contents' => 'required|array',
            'article.published_at' => ['nullable', 'date', resolve(ReservationPublishedAt::class)],
            'article.articles' => 'present|array|max:10',
            'article.articles.*' => 'required|distinct|exists:articles,id,status,publish',
            'should_notify' => 'nullable|boolean',
            'without_update_modified_at' => 'nullable|boolean',
            'follow_redirect' => 'nullable|boolean',
        ];
    }
}
