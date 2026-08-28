<?php

declare(strict_types=1);

return [
    /*
     * rcloneバイナリのパス。ローカルはPATHが通っている前提で`rclone`のみ、
     * 本番はサーバーに手動設置したパス(例: /home/simutrans/bin/rclone)を.envで指定する。
     */
    'binary_path' => env('RCLONE_BINARY_PATH', 'rclone'),

    /*
     * ユーザーアップロードファイルの同期元(ローカル)。
     */
    'uploads_source' => storage_path('app/public/user'),

    /*
     * ユーザーアップロードファイルの同期先(rcloneのremote:path形式)。
     * Dropbox側の認証情報はfilesystems.php の 'dropbox' ディスクと同じもの
     * (DROPBOX_APP_KEY/DROPBOX_APP_SECRET/DROPBOX_REFRESH_TOKEN)をrclone.confに設定して使う。
     */
    'uploads_remote' => env('RCLONE_DROPBOX_REMOTE', 'dropbox:Simutrans Addon Portal/user-uploads'),
];
