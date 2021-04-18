<?php

namespace App\Console\Commands;

use App\Jobs\Attachments\JobCompressImage;
use Illuminate\Console\Command;

/**
 * @see https://tinypng.com/dashboard/api
 */
class CommandCompressImage extends Command
{
    protected $signature = 'compress:image';

    protected $description = 'tinypng api経由で画像を圧縮する';

    public function handle()
    {
        JobCompressImage::dispatch();

        return 0;
    }
}
