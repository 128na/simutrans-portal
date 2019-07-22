<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attachment;
use App\Models\CompressedImage;
use Illuminate\Support\Facades\Storage;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Tinify\setKey(config('app.tinypng_api_key'));

        foreach (Attachment::cursor() as $attachment) {
            $this->info($attachment->path);
            if(!Storage::disk('public')->exists($attachment->path)) {
                $this->warn('ファイルがありません');
                continue;
            }
            if(!$attachment->is_png) {
                $this->info('PNG画像以外');
                continue;
            }
            if(CompressedImage::isCompressed($attachment->path)) {
                $this->info('圧縮済み');
                continue;
            }
            $path = $attachment->path;
            $backup_path = $path.'.bak';
            try {
                Storage::disk('public')->copy($path, $backup_path);
                $source = \Tinify\fromFile(Storage::disk('public')->path($path));
                $source->toFile(Storage::disk('public')->path($path));

                CompressedImage::create(['path' => $path]);
                Storage::disk('public')->delete($backup_path);
                $this->info('圧縮成功');
            } catch(\Throwable $e) {
                $this->error('圧縮失敗');
                $this->error($e->getMessage());
                Storage::disk('public')->delete($path);
                Storage::disk('public')->move($backup_path, $path);
            }
        }
    }
}
