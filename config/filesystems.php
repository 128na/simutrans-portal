<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
     */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
     */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
     */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'local_backup' => [
            'driver' => 'local',
            'root' => base_path('backups'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'permissions' => [
                'file' => [
                    'public' => 0664,
                    'private' => 0664,
                ],
                'dir' => [
                    'public' => 0775,
                    'private' => 0775,
                ],
            ],
        ],

        'dropbox' => [
            'driver' => 'dropbox',
            'refreshToken' => env('DROPBOX_REFRESH_TOKEN'),
            'appKey' => env('DROPBOX_APP_KEY'),
            'appSecret' => env('DROPBOX_APP_SECRET'),
        ],
        'ftp' => [
            'driver' => 'ftp',
            'host' => 'simutrans.sakura.ne.jp',
            'root' => 'backup/',
            'username' => env('SFTP_USER'),
            'password' => env('SFTP_PASSWORD'),
        ],
    ],
];
