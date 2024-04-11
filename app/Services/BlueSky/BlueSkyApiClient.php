<?php

declare(strict_types=1);

namespace App\Services\BlueSky;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\Screenshot;
use App\Models\User;
use App\Services\Attachment\FileSizeBaseResizer;
use App\Services\Front\MetaOgpService;
use potibm\Bluesky\BlueskyApi;
use potibm\Bluesky\BlueskyPostService;
use potibm\Bluesky\Feed\Post;
use potibm\Bluesky\Response\RecordResponse;

/**
 * @see https://www.docs.bsky.app/docs
 */
final readonly class BlueSkyApiClient
{
    public function __construct(
        private readonly BlueskyApi $blueskyApi,
        private readonly BlueskyPostService $blueskyPostService,
        private readonly MetaOgpService $metaOgpService,
        private readonly FileSizeBaseResizer $fileSizeBaseResizer,
    ) {
    }

    /**
     * @see https://github.com/potibm/phluesky
     */
    public function send(Post $post): RecordResponse
    {
        return $this->blueskyApi->createRecord($post);
    }

    /**
     * @see https://github.com/potibm/phluesky?tab=readme-ov-file#adding-website-card-embeds
     */
    public function addWebsiteCard(Post $post, Article $article): Post
    {
        assert($article->user instanceof User);
        $ogp = $this->metaOgpService->show($article->user, $article);
        $thumbnail = null;
        if ($article->hasThumbnail) {
            assert($article->thumbnail instanceof Attachment);
            $thumbnail = $this->fileSizeBaseResizer->resize($article->thumbnail->fullPath, 10 ** 6);
        }

        return $this->blueskyPostService->addWebsiteCard(
            $post,
            $ogp['canonical'],
            $ogp['title'],
            $ogp['description'],
            $thumbnail,
        );
    }

    public function addWebsiteCardScreenshot(Post $post, Screenshot $screenshot): Post
    {
        assert($screenshot->user instanceof User);
        $ogp = $this->metaOgpService->screenshot($screenshot);
        $thumbnail = $this->fileSizeBaseResizer->resize($ogp['image'], 10 ** 6);

        return $this->blueskyPostService->addWebsiteCard(
            $post,
            $ogp['canonical'],
            $ogp['title'],
            $ogp['description'],
            $thumbnail,
        );
    }
}
