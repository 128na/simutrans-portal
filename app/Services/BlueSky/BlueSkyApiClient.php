<?php

declare(strict_types=1);

namespace App\Services\BlueSky;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
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
        private BlueskyApi $blueskyApi,
        private BlueskyPostService $blueskyPostService,
        private MetaOgpService $metaOgpService,
        private ResizeByFileSize $resizeByFileSize,
    ) {}

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
        if (! $article->user instanceof User) {
            throw new \RuntimeException('Article user is required');
        }

        $ogp = $this->metaOgpService->frontArticleShow($article->user, $article);
        $thumbnail = null;
        if ($article->hasThumbnail && $article->thumbnail instanceof Attachment) {
            $thumbnail = ($this->resizeByFileSize)($article->thumbnail->fullPath, 10 ** 6);
        }

        return $this->blueskyPostService->addWebsiteCard(
            $post,
            $ogp['canonical'],
            $ogp['title'],
            $ogp['description'],
            $thumbnail,
        );
    }
}
