<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use ZipArchive;

final class ZipErrorException extends Exception
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
        return match ($code) {
            ZipArchive::ER_OK => 'エラーはありません。',
            ZipArchive::ER_MULTIDISK => '複数ディスクの zip アーカイブはサポートされません。',
            ZipArchive::ER_RENAME => '一時ファイルの名前変更に失敗しました。',
            ZipArchive::ER_CLOSE => 'zip アーカイブのクローズに失敗しました。',
            ZipArchive::ER_SEEK => 'シークエラー。',
            ZipArchive::ER_READ => '読み込みエラー。',
            ZipArchive::ER_WRITE => '書き込みエラー。',
            ZipArchive::ER_CRC => 'CRC エラー。',
            ZipArchive::ER_ZIPCLOSED => 'zip アーカイブはクローズされました。',
            ZipArchive::ER_NOENT => 'そのファイルはありません。',
            ZipArchive::ER_EXISTS => 'ファイルが既に存在します。',
            ZipArchive::ER_OPEN => 'ファイルをオープンできません。',
            ZipArchive::ER_TMPOPEN => '一時ファイルの作成に失敗しました。',
            ZipArchive::ER_ZLIB => 'Zlib エラー。',
            ZipArchive::ER_MEMORY => 'メモリの確保に失敗しました。',
            ZipArchive::ER_CHANGED => 'エントリが変更されました。',
            ZipArchive::ER_COMPNOTSUPP => '圧縮方式がサポートされていません。',
            ZipArchive::ER_EOF => '予期せぬ EOF です。',
            ZipArchive::ER_INVAL => '無効な引数です。',
            ZipArchive::ER_NOZIP => 'zip アーカイブではありません。',
            ZipArchive::ER_INTERNAL => '内部エラー。',
            ZipArchive::ER_INCONS => '矛盾した Zip アーカイブです。',
            ZipArchive::ER_REMOVE => 'ファイルを削除できません。',
            ZipArchive::ER_DELETED => 'エントリが削除されました。',
            ZipArchive::ER_ENCRNOTSUPP => '暗号化メソッドはサポートされていません。 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。',
            ZipArchive::ER_RDONLY => '読み取り専用のアーカイブです。 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。',
            ZipArchive::ER_NOPASSWD => 'パスワードが指定されていません。 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。',
            ZipArchive::ER_WRONGPASSWD => '間違ったパスワードが指定されました。 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。',
            ZipArchive::ER_CANCELLED => '操作がキャンセルされました。 libzip ≥ 1.6.0 でビルドした場合、 PHP 7.4.3 以降、PECL zip 1.16.1 以降で利用可能です。',
            default => '不明なエラー',
        };
    }
}
