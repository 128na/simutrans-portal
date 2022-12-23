<?php

namespace App\Http\Resources\Api\Mypage;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'post_type' => $this->post_type,
            'contents' => $this->contents,
            'categories' => $this->categories->map(fn (Category $c) => [
                'id' => $c->id,
                'name' => __("category.{$c->type}.{$c->slug}"),
                'type' => $c->type,
                'slug' => $c->slug,
            ]),
            'tags' => $this->tags->map(fn (Tag $t) => [
                'id' => $t->id,
                'name' => $t->name,
            ]),
            'created_at' => $this->created_at->toIso8601String(),
            'published_at' => $this->published_at?->toIso8601String(),
            'modified_at' => $this->modified_at?->toIso8601String(),
            'url' => route('articles.show', $this->slug),
            'metrics' => [
                'totalViewCount' => $this->totalViewCount->count ?? 0,
                'totalConversionCount' => $this->totalConversionCount->count ?? 0,
                'totalRetweetCount' => $this->tweetLogSummary?->total_retweet_count ?? 0,
                'totalReplyCount' => $this->tweetLogSummary?->total_reply_count ?? 0,
                'totalLikeCount' => $this->tweetLogSummary?->total_like_count ?? 0,
                'totalQuoteCount' => $this->tweetLogSummary?->total_quote_count ?? 0,
                'totalImpressionCount' => $this->tweetLogSummary?->total_impression_count ?? 0,
                'totalUrlLinkClicks' => $this->tweetLogSummary?->total_url_link_clicks ?? 0,
                'totalUserProfileClicks' => $this->tweetLogSummary?->total_user_profile_clicks ?? 0,
            ],
        ];
    }
}
