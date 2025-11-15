<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Enums\ArticleStatus;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

final class PublishReservation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:publish-reservation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予約投稿が時が来たとき公開ステータスに更新する';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CarbonImmutable $now,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $lazyCollection = $this->articleRepository->cursorReservations($this->now);
        $changed = false;
        foreach ($lazyCollection as $article) {
            $article->update([
                'status' => ArticleStatus::Publish,
                'modified_at' => $article->published_at,
            ]);
            $changed = true;
        }

        if ($changed) {
            dispatch_sync(new \App\Jobs\Article\JobUpdateRelated());
        }

        return 0;
    }
}
