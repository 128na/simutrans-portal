<?php

declare(strict_types=1);

namespace App\Jobs\Article;

use App\Actions\ArticleSearchIndex\UpdateOrCreateAction;
use App\Actions\GenerateStatic\DeleteUnrelatedTags;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 記事更新に連動する関連データを更新する.
 */
class JobUpdateRelated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * リトライ回数
     */
    public int $tries = 3;

    /**
     * タイムアウト（秒）
     */
    public int $timeout = 60;

    public function __construct(private readonly ?int $articleId = null) {}

    public function handle(
        DeleteUnrelatedTags $deleteUnrelatedTags,
        UpdateOrCreateAction $updateOrCreateAction,
    ): void {
        $deleteUnrelatedTags();
        if ($this->articleId) {
            $updateOrCreateAction($this->articleId);
        }
    }

    /**
     * ジョブ失敗時の処理
     */
    public function failed(?\Throwable $throwable): void
    {
        Log::error('JobUpdateRelated failed', [
            'article_id' => $this->articleId,
            'exception' => $throwable?->getMessage(),
        ]);
    }
}
