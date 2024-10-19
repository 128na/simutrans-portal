<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

use App\Enums\SitemapPriority;
use App\Models\Article;
use Closure;
use Illuminate\Support\Collection;
use Spatie\Sitemap\Tags\Url;

final class CreateArticleSitemaps
{
    public function __invoke(SitemapHandler $sitemapHandler): void
    {
        $this->getChunk(function (Collection $articles, int $index) use ($sitemapHandler): void {
            $filename = sprintf('articles_%03d.xml', $index);
            foreach ($articles as $article) {
                $url = route('articles.show', [
                    'userIdOrNickname' => $article->user->nickname ?? $article->user_id,
                    'articleSlug' => $article->slug,
                ]);

                $urlTag = Url::create($url);

                if ($article->published_at) {
                    $urlTag->setLastModificationDate($article->published_at);
                }

                if ($article->has_thumbnail) {
                    $urlTag->addImage($article->thumbnail_url, $article->title);
                }

                $sitemapHandler->add($filename, $urlTag);
            }
        });
    }

    /**
     * @param  Closure(Collection<int,Article> $articles,int $index):void  $fn
     */
    private function getChunk(Closure $fn): void
    {
        Article::active()->with('user', 'attachments')->chunkById(100, $fn);
    }
}
