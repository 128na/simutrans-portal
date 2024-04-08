<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

use App\Models\Screenshot;
use Illuminate\Support\Collection;
use Spatie\Sitemap\Tags\Url;

final class CreateScreenshotSitemaps
{
    public function __invoke(SitemapHandler $sitemapHandler): void
    {
        Screenshot::publish()->with('attachments')
            ->chunkById(100, function (Collection $screenshots, int $index) use ($sitemapHandler): void {
                $filename = sprintf('screenshot_%03d.xml', $index);
                foreach ($screenshots as $screenshot) {
                    $url = route('screenshots.show', $screenshot);

                    $urlTag = Url::create($url)
                        ->setChangeFrequency('monthly')
                        ->setLastModificationDate($screenshot->published_at)
                        ->setPriority(0.7);

                    foreach ($screenshot->attachments as $attachment) {
                        $urlTag->addImage($attachment->url,
                            sprintf('%s', $attachment->caption ?: $screenshot->title));
                    }

                    $sitemapHandler->add($filename, $urlTag);
                }
            });
    }
}
