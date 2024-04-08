<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

final class Create
{
    public function __construct(
        private readonly SitemapHandler $sitemapHandler,
        private readonly CreateArticleSitemaps $createArticleSitemaps,
        private readonly CreateScreenshotSitemaps $createScreenshotSitemaps,
        private readonly CreateListSitemaps $createListSitemaps,
    ) {

    }

    public function __invoke(): void
    {
        $this->sitemapHandler->destroyAll();
        ($this->createArticleSitemaps)($this->sitemapHandler);
        ($this->createScreenshotSitemaps)($this->sitemapHandler);
        ($this->createListSitemaps)($this->sitemapHandler);

        $this->sitemapHandler->write();
    }
}
