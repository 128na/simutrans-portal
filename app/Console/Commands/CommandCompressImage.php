<?php

namespace App\Console\Commands;

use App\Jobs\Attachments\JobCompressImage;
use Illuminate\Console\Command;
use Throwable;

/**
 * @see https://tinypng.com/dashboard/api
 */
class CommandCompressImage extends Command
{
    protected $signature = 'compress:image';

    protected $description = 'tinypng api経由で画像を圧縮する';

    public function handle(): int
    {
        try {
            JobCompressImage::dispatchSync();
        } catch (Throwable $e) {
            report($e);

            return 1;
        }

        return 0;
    }
}
