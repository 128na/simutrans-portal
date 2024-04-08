<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

use App\Models\Screenshot;
use Closure;
use Illuminate\Support\Collection;
use Spatie\Sitemap\Tags\Url;

final class CreateScreenshotSitemaps
{
    public function __invoke(SitemapHandler $sitemapHandler): void
    {
        $this->getChunk(function (Collection $screenshots, int $index) use ($sitemapHandler): void {
            $filename = sprintf('screenshot_%03d.xml', $index);
            foreach ($screenshots as $screenshot) {
                $url = route('screenshots.show', $screenshot);

                $urlTag = Url::create($url)
                    ->setChangeFrequency('monthly')
                    ->setPriority(0.7);

                if ($screenshot->published_at) {
                    $urlTag->setLastModificationDate($screenshot->published_at);
                }

                foreach ($screenshot->attachments as $attachment) {
                    $urlTag->addImage($attachment->url,
                        sprintf('%s', $attachment->caption ?: $screenshot->title));
                }

                $sitemapHandler->add($filename, $urlTag);
            }
        });
    }

    /**
     * @param  Closure(Collection<int,Screenshot> $screenshots,int $index):void  $fn
     */
    private function getChunk(Closure $fn): void
    {
        Screenshot::publish()->with('attachments')->chunkById(100, $fn);
    }
}
