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

/**
 * 記事更新に連動する関連データを更新する.
 */
class JobUpdateRelated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
}
