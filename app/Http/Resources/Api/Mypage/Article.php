<?php

namespace App\Http\Resources\Api\Mypage;

use Illuminate\Http\Resources\Json\JsonResource;

class Article extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
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
            'categories' => $this->categories->pluck('id'),
            'tags' => $this->tags->pluck('name'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'url' => route('articles.show', $this->slug),
            'metrics' => [
                'totalViewCount' => $this->totalViewCount->count ?? 0,
                'totalConversionCount' => $this->totalConversionCount->count ?? 0,
                'totalRetweetCount' => $this->tweetLogSummary?->total_retweet_count ?? 0,
                'totalReplyCount' => $this->tweetLogSummary?->total_reply_count ?? 0,
                'totalLikeCount' => $this->tweetLogSummary?->total_like_count ?? 0,
                'totalQuoteCount' => $this->tweetLogSummary?->total_quote_count ?? 0,
            ],
        ];
    }
}
