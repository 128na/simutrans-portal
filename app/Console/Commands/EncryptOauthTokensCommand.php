<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * oauth_tokens テーブルの access_token / refresh_token を暗号化する一時コマンド.
 *
 * OauthToken モデルに encrypted キャストを追加する前に、既存の平文データを
 * 一度だけ暗号化するために実行する。反映確認後、フォローアップで削除する。
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
    protected $description = 'oauth_tokens テーブルの既存の平文トークンを暗号化する（一度だけ実行）';

    public function handle(): int
    {
        $rows = DB::table('oauth_tokens')->get();

        foreach ($rows as $row) {
            DB::table('oauth_tokens')
                ->where('application', $row->application)
                ->update([
                    'access_token' => Crypt::encryptString($row->access_token),
                    'refresh_token' => Crypt::encryptString($row->refresh_token),
                ]);

            $this->info("Encrypted tokens for application: {$row->application}");
        }

        $this->info(sprintf('Done. %d row(s) processed.', count($rows)));

        return Command::SUCCESS;
    }
}
