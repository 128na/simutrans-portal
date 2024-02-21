<?php

declare(strict_types=1);

namespace App\Providers;

use App\Adapters\AutoRefreshingDropBoxTokenService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // long term tokenが作れなくなっているので都度生成する
        // https://github.com/spatie/flysystem-dropbox/issues/86
        Storage::extend('dropbox', static function ($app, $config) {
            $token = new AutoRefreshingDropBoxTokenService;
            $client = new DropboxClient(
                $token->getToken($config['appKey'],
                    $config['appSecret'],
                    $config['refreshToken'])
            );
            $adapter = new DropboxAdapter($client);
            $driver = new Filesystem($adapter, ['case_sensitive' => false]);

            return new FilesystemAdapter($driver, $adapter);
        });
    }
}
