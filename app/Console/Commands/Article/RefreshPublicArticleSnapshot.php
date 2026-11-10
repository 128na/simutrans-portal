<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Actions\FrontArticle\LatestAction;
use App\Models\Article;
use App\Repositories\Article\FrontArticleRepository;
use App\Services\Front\PublicArticleSnapshot;
use Illuminate\Console\Command;
use Throwable;

class RefreshPublicArticleSnapshot extends Command
{
    protected $signature = 'article:refresh-public-snapshot';

    protected $description = '公開済みの新着とお知らせの表示データを生成する';

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

                return ['latest' => $latest, 'announces' => array_values($announces->all())];
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
}
