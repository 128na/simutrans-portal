<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Enums\ArticleStatus;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublishReservation extends Command
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
        $errorCount = 0;
        foreach ($lazyCollection as $article) {
            try {
                $published = DB::transaction(function () use ($article): bool {
                    $current = Article::query()
                        ->whereKey($article->id)
                        ->where('status', ArticleStatus::Reservation)
                        ->where('published_at', '<=', $this->now)
                        ->lockForUpdate()
                        ->first();

                    return $current?->update([
                        'status' => ArticleStatus::Publish,
                        'modified_at' => $current->published_at,
                    ]) ?? false;
                });
                $changed = $changed || $published;
            } catch (\Throwable $throwable) {
                $errorCount++;
                Log::error('Failed to publish reserved article', [
                    'article_id' => $article->id,
                    'error' => $throwable->getMessage(),
                ]);
            }
        }

        if ($changed) {
            dispatch_sync(new JobUpdateRelated);
        }

        return $errorCount > 0 ? self::FAILURE : self::SUCCESS;
    }
}
