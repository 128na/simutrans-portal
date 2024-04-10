<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Article;

use App\Constants\NgWords;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Rules\NgWordRule;
use App\Rules\NotJustNumbers;
use App\Rules\ReservationPublishedAt;
use App\Rules\UniqueSlugByUser;
use Illuminate\Validation\Rule;

final class StoreRequest extends BaseRequest
{
    /**
     * @return array<mixed>
     */
    protected function baseRule(): array
    {
        return [
            'article.post_type' => ['bail', 'required', Rule::enum(ArticlePostType::class)],
            'article.status' => ['required', Rule::enum(ArticleStatus::class)],
            'article.title' => ['required', 'max:255', 'unique:articles,title', new NgWordRule(NgWords::ARTICLE_TITLE)],
            'article.slug' => ['required', 'max:255', new NotJustNumbers, new UniqueSlugByUser],
            'article.published_at' => ['nullable', 'date', app(ReservationPublishedAt::class)],
            'article.articles' => 'present|array|max:10',
            'article.articles.*.id' => 'required|distinct|exists:articles,id,status,publish',
            'should_notify' => 'nullable|boolean',
        ];
    }
}
