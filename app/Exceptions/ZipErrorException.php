<?php

namespace App\Exceptions;

use Exception;
use ZipArchive;

class ZipErrorException extends Exception
{
    public function __construct(string $message, ?int $code = null)
    {
        if (! is_null($code)) {
            $message .= ' '.$this->handleCode($code);
        }
        parent::__construct($message);
    }

    /**
     * @see https://www.php.net/manual/ja/zip.constants.php
     */
    private function handleCode(?int $code): string
    {
        switch ($code) {
            case ZipArchive::ER_OK:
                return 'エラーはありません。';
            case ZipArchive::ER_MULTIDISK:
                return '複数ディスクの zip アーカイブはサポートされません。';
            case ZipArchive::ER_RENAME:
                return '一時ファイルの名前変更に失敗しました。';
            case ZipArchive::ER_CLOSE:
                return 'zip アーカイブのクローズに失敗しました。';
            case ZipArchive::ER_SEEK:
                return 'シークエラー。';
            case ZipArchive::ER_READ:
                return '読み込みエラー。';
            case ZipArchive::ER_WRITE:
                return '書き込みエラー。';
            case ZipArchive::ER_CRC:
                return 'CRC エラー。';
            case ZipArchive::ER_ZIPCLOSED:
                return 'zip アーカイブはクローズされました。';
            case ZipArchive::ER_NOENT:
                return 'そのファイルはありません。';
            case ZipArchive::ER_EXISTS:
                return 'ファイルが既に存在します。';
            case ZipArchive::ER_OPEN:
                return 'ファイルをオープンできません。';
            case ZipArchive::ER_TMPOPEN:
                return '一時ファイルの作成に失敗しました。';
            case ZipArchive::ER_ZLIB:
                return 'Zlib エラー。';
            case ZipArchive::ER_MEMORY:
                return 'メモリの確保に失敗しました。';
            case ZipArchive::ER_CHANGED:
                return 'エントリが変更されました。';
            case ZipArchive::ER_COMPNOTSUPP:
                return '圧縮方式がサポートされていません。';
            case ZipArchive::ER_EOF:
                return '予期せぬ EOF です。';
            case ZipArchive::ER_INVAL:
                return '無効な引数です。';
            case ZipArchive::ER_NOZIP:
                return 'zip アーカイブではありません。';
            case ZipArchive::ER_INTERNAL:
                return '内部エラー。';
            case ZipArchive::ER_INCONS:
                return '矛盾した Zip アーカイブです。';
            case ZipArchive::ER_REMOVE:
                return 'ファイルを削除できません。';
            case ZipArchive::ER_DELETED:
                return 'エントリが削除されました。';
            case ZipArchive::ER_ENCRNOTSUPP:
                return '暗号化メソッドはサポートされていません。 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。';
            case ZipArchive::ER_RDONLY:
                return '読み取り専用のアーカイブです。 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。';
            case ZipArchive::ER_NOPASSWD:
                return 'パスワードが指定されていません。 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。';
            case ZipArchive::ER_WRONGPASSWD:
                return '間違ったパスワードが指定されました。 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。';
            case ZipArchive::ER_CANCELLED:
                return '操作がキャンセルされました。 libzip ≥ 1.6.0 でビルドした場合、 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。';
        }
    }
}
