<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * oauth_tokens テーブルの access_token / refresh_token を暗号化する一時コマンド.
 *
 * OauthToken モデルに encrypted キャストを追加する前に、既存の平文データを
 * 暗号化するために実行する。既に暗号化済みの値はスキップするため複数回
 * 実行しても安全。反映確認後、フォローアップで削除する。
 */
class EncryptOauthTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth-tokens:encrypt-existing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'oauth_tokens テーブルの既存の平文トークンを暗号化する（再実行しても安全）';

    public function handle(): int
    {
        if (Schema::getColumnType('oauth_tokens', 'access_token') !== 'text') {
            $this->error('oauth_tokens.access_token/refresh_tokenがtext型に拡張されていません。先にマイグレーションを実行してください。');

            return Command::FAILURE;
        }

        $rows = DB::table('oauth_tokens')->get();
        $encryptedCount = 0;

        foreach ($rows as $row) {
            if ($this->isAlreadyEncrypted($row->access_token) && $this->isAlreadyEncrypted($row->refresh_token)) {
                $this->info("Already encrypted, skipped: {$row->application}");

                continue;
            }

            DB::table('oauth_tokens')
                ->where('application', $row->application)
                ->update([
                    'access_token' => Crypt::encryptString($row->access_token),
                    'refresh_token' => Crypt::encryptString($row->refresh_token),
                ]);

            $this->info("Encrypted tokens for application: {$row->application}");
            $encryptedCount++;
        }

        $this->info(sprintf(
            'Done. %d row(s) encrypted, %d row(s) already encrypted.',
            $encryptedCount,
            count($rows) - $encryptedCount
        ));

        return Command::SUCCESS;
    }

    private function isAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }
}
