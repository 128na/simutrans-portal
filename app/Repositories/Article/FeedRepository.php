<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class FeedRepository
{
    public function __construct(
        private Article $article,
        private Category $category,
    ) {
    }

    public function addon(): Collection
    {
        return $this->article
            ->active()
            ->addon()
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents', 'modified_at')
            ->limit(24)
            ->with('user:id,name')
            ->orderBy('modified_at', 'desc')
            ->get();
    }

    public function pakAddon(string $pakSlug): Collection
    {
        $category = $this->category->pak()->slug($pakSlug)->firstOrFail();

        return $category->articles()
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents', 'modified_at')
            ->active()
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents', 'modified_at')
            ->limit(24)
            ->with('user:id,name')
            ->orderBy('modified_at', 'desc')
            ->get();
    }

    public function page(): Collection
    {
        return $this->article
            ->active()
            ->withoutAnnounce()
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents', 'modified_at')
            ->with('user:id,name')
            ->orderBy('modified_at', 'desc')
            ->get();
    }

    public function announce(): Collection
    {
        return $this->article
            ->active()
            ->announce()
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents', 'modified_at')
            ->with('user:id,name')
            ->orderBy('modified_at', 'desc')
            ->get();
    }
}
