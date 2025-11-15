<?php

declare(strict_types=1);

namespace App\Jobs\Article;

use App\Actions\GenerateStatic\DeleteUnrelatedTags;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 記事更新に連動する関連データを更新する.
 */
final class JobUpdateRelated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(
        DeleteUnrelatedTags $deleteUnrelatedTags,
    ): void {
        $deleteUnrelatedTags();
    }
}
