<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

final readonly class Create
{
    public function __construct(
        private SitemapHandler $sitemapHandler,
        private CreateArticleSitemaps $createArticleSitemaps,
        private CreateListSitemaps $createListSitemaps,
    ) {}

    public function __invoke(): void
    {
        $this->sitemapHandler->destroyAll();
        ($this->createArticleSitemaps)($this->sitemapHandler);
        ($this->createListSitemaps)($this->sitemapHandler);

        $this->sitemapHandler->write();
    }
}
