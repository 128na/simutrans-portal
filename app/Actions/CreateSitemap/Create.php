<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

use App\Models\Article;
use Carbon\CarbonImmutable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Sitemap;
use Spatie\Sitemap\Tags\Url;

final class Create
{
    public function __construct(private readonly FilesystemAdapter $filesystem)
    {

    }

    public function __invoke(string $siteurl): void
    {
        $gen = SitemapGenerator::create($siteurl);
        $sitemaps = [];

        Article::active()->with('user', 'attachments')
            ->chunkById(100, function (Collection $articles, int $index) use ($gen, &$sitemaps) {
                $sitemap = $gen->getSitemap();
                $filename = sprintf('sitemap_%03d.xml', $index);
                /** @var Article */
                foreach ($articles as $article) {
                    $sitemap->add($this->createUrl($article));
                }
                $sitemap->writeToFile($this->filesystem->path($filename));

                $sitemaps[] = $filename;
            });

        $index = SitemapIndex::create();
        foreach ($sitemaps as $sitemap) {
            $index->add(Sitemap::create($this->filesystem->url($sitemap))->setLastModificationDate(CarbonImmutable::now()));
        }
        $index->writeToFile($this->filesystem->path('index.xml'));
    }

    private function createUrl(Article $article): Url
    {
        $url = Url::create($this->getUrl($article))
            ->setLastModificationDate($article->published_at)
            ->setPriority(0.5);
        if ($article->has_thumbnail) {
            $url->addImage($article->thumbnail_url, $article->title);
        }

        return $url;
    }

    private function getUrl(Article $article): string
    {
        return route('articles.show', [
            'userIdOrNickname' => $article->user->nickname ?? $article->user_id,
            'articleSlug' => $article->slug,
        ]);

    }
}
