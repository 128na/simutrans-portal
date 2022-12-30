<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'status' => $this->resource->status,
            'post_type' => $this->resource->post_type,
            'contents' => $this->resource->contents,
            'categories' => $this->resource->categories->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => __("category.{$c->type}.{$c->slug}"),
                'type' => $c->type,
                'slug' => $c->slug,
            ]),
            'tags' => $this->resource->tags->map(fn (Tag $t) => [
                'id' => $t->id,
                'name' => $t->name,
            ]),
            'created_at' => $this->resource->created_at->toIso8601String(),
            'published_at' => $this->resource->published_at?->toIso8601String(),
            'modified_at' => $this->resource->modified_at?->toIso8601String(),
            'url' => route('articles.show', $this->resource->slug),
            'metrics' => [
                'totalViewCount' => $this->resource->totalViewCount->count ?? 0,
                'totalConversionCount' => $this->resource->totalConversionCount->count ?? 0,
                'totalRetweetCount' => $this->resource->tweetLogSummary?->total_retweet_count ?? 0,
                'totalReplyCount' => $this->resource->tweetLogSummary?->total_reply_count ?? 0,
                'totalLikeCount' => $this->resource->tweetLogSummary?->total_like_count ?? 0,
                'totalQuoteCount' => $this->resource->tweetLogSummary?->total_quote_count ?? 0,
                'totalImpressionCount' => $this->resource->tweetLogSummary?->total_impression_count ?? 0,
                'totalUrlLinkClicks' => $this->resource->tweetLogSummary?->total_url_link_clicks ?? 0,
                'totalUserProfileClicks' => $this->resource->tweetLogSummary?->total_user_profile_clicks ?? 0,
            ],
        ];
    }
}
