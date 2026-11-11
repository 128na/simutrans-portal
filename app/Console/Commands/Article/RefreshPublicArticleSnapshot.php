<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Actions\FrontArticle\LatestAction;
use App\Enums\ArticlePostType;
use App\Models\Article;
use App\Repositories\Article\FrontArticleRepository;
use App\Services\Front\PublicArticleSnapshot;
use Illuminate\Console\Command;
use Throwable;

class RefreshPublicArticleSnapshot extends Command
{
    protected $signature = 'article:refresh-public-snapshot';

    protected $description = '公開済みの新着・お知らせと pak × 種類の件数の表示データを生成する';

    public function handle(PublicArticleSnapshot $snapshot, LatestAction $latestAction, FrontArticleRepository $articles): int
    {
        try {
            $updated = $snapshot->refresh(function () use ($latestAction, $articles): array {
                $latest = [];
                foreach (PublicArticleSnapshot::PAKS as $pak) {
                    $latest[$pak] = $latestAction->cardsByPak($pak);
                }

                $announces = $articles->getAnnounces()->load('user:id,nickname')->map(
                    fn (Article $article): array => [
                        'title' => $article->title,
                        'url' => $article->showUrl(),
                        'date' => ($article->modified_at ?? $article->published_at)?->format('Y/m/d') ?? '',
                    ]
                );

                return [
                    'latest' => $latest,
                    'announces' => array_values($announces->all()),
                    'pak_addon_counts' => $this->pakAddonCounts($articles),
                ];
            });
        } catch (Throwable $error) {
            report($error);
            $this->error('公開記事の表示データを生成できませんでした。');

            return self::FAILURE;
        }

        $this->info($updated
            ? '公開記事の表示データを生成しました。 '.now()->toIso8601String()
            : '公開記事の表示データの更新を見送りました。別の生成または公開状態の変更と重なりました。');

        return self::SUCCESS;
    }

    /**
     * 主要3 pak × 種類ごとの公開記事数。0件の組み合わせは持たない
     *
     * @return array<array-key, array<string, array{total: int, addon_post: int}>>
     */
    private function pakAddonCounts(FrontArticleRepository $articles): array
    {
        $counts = array_fill_keys(PublicArticleSnapshot::PAKS, []);
        foreach ($articles->countByPakAddon(PublicArticleSnapshot::PAKS) as $row) {
            $count = $counts[$row['pak']][$row['addon']] ?? ['total' => 0, 'addon_post' => 0];
            $count['total'] += $row['count'];
            if ($row['post_type'] === ArticlePostType::AddonPost->value) {
                $count['addon_post'] += $row['count'];
            }
            $counts[$row['pak']][$row['addon']] = $count;
        }

        return $counts;
    }
}
