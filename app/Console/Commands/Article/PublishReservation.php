<?php

namespace App\Console\Commands\Article;

use App\Jobs\Article\JobUpdateRelated;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

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
        private ArticleRepository $articleRepository,
        private CarbonImmutable $now,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cusror = $this->articleRepository->cursorReservations($this->now);
        $changed = false;
        foreach ($cusror as $article) {
            $article->update([
                'status' => config('status.publish'),
                'modified_at' => $article->published_at,
            ]);
            $changed = true;
        }

        if ($changed) {
            JobUpdateRelated::dispatchSync();
        }

        return 0;
    }
}
