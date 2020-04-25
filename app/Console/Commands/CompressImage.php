<?php

namespace App\Console\Commands;

use App\Models\CompressedImage;
use App\Services\CompressedImageService;
use Illuminate\Console\Command;

/**
 * tinypng api経由で画像を圧縮する
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
     * @var CompressedImageService
     */
    private $compressed_image_service;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CompressedImageService $compressed_image_service)
    {
        parent::__construct();
        $this->compressed_image_service = $compressed_image_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = $this->compressed_image_service->getAttachmentsCursor()->each(function ($attachment) {
            $this->info($attachment->path);

            try {
                $res = $this->compressed_image_service->compressIfNeeded($attachment);
                $this->info($res);
            } catch (\Throwable $e) {
                $this->error('圧縮失敗');
                $this->error($e->getMessage());
            }
        })->count();
        logger("$count image compressed");

        return 0;
    }
}
