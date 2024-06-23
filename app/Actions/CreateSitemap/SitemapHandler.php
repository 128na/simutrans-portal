<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

use Carbon\CarbonImmutable;
use Illuminate\Filesystem\FilesystemAdapter;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Sitemap as TagsSitemap;
use Spatie\Sitemap\Tags\Url;

final class SitemapHandler
{
    /**
     * @var array<string,Sitemap>
     */
    private array $sitemaps = [];

    public function __construct(
        private readonly FilesystemAdapter $filesystemAdapter,
    ) {}

    public function destroyAll(): void
    {
        $files = $this->filesystemAdapter->allFiles('/');

        foreach ($files as $file) {
            if (str_ends_with((string) $file, '.xml')) {
                $this->filesystemAdapter->delete($file);
            }
        }
    }

    public function add(string $filename, Url $url): void
    {
        if (! array_key_exists($filename, $this->sitemaps)) {
            $this->sitemaps[$filename] = Sitemap::create();
        }

        $this->sitemaps[$filename]->add($url);
    }

    public function write(): void
    {
        foreach ($this->sitemaps as $filename => $sitemap) {
            $sitemap->writeToFile($this->filesystemAdapter->path($filename));
        }

        $this->writeIndex();
    }

    private function writeIndex(): void
    {
        $sitemapIndex = SitemapIndex::create();
        array_map(function (string $filename) use ($sitemapIndex): void {
            $sitemapIndex->add(TagsSitemap::create($this->filesystemAdapter->url($filename))
                ->setLastModificationDate(CarbonImmutable::now()));
        }, array_keys($this->sitemaps));
        $sitemapIndex->writeToFile($this->filesystemAdapter->path('index.xml'));
    }
}
