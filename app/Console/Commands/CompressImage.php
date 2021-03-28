<?php

namespace App\Console\Commands;

use App\Jobs\Attachments\JobCompressImage;
use Illuminate\Console\Command;

/**
 * tinypng api経由で画像を圧縮する.
 *
 * @see https://tinypng.com/dashboard/api
 */
class CompressImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compress:image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compress Image via tinypng.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch_now(app(JobCompressImage::class));

        return 0;
    }
}
