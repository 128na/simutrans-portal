<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class SyncUserUploads
{
    /**
     * 共有レンタルサーバーでは長時間動くプロセスが強制終了(SIGKILL)されるため、
     * 1回の転送時間を短く区切る。--cutoff-mode SOFTと組み合わせ、実行中のファイル
     * 転送は完了させてから打ち切る。取りこぼした分は次回のcron実行(rcloneの差分検出)で続きから転送される。
     */
    private const string MAX_DURATION = '25s';

    /**
     * --max-duration+SOFTは実行中のファイル転送を完了させてから打ち切るため、
     * 大きいファイル(最大300MB超)は25秒を超えて転送が続くことがある。
     * ここで先に強制終了すると--max-durationの意図(安全な自己終了)を潰してしまうため、
     * 明らかにハングした場合のみ止める保険として十分大きい値にする。
     */
    private const int TIMEOUT_SECONDS = 120;

    /**
     * rcloneが--max-durationで打ち切った際の終了コード。時間切れによる想定内の中断であり、失敗として扱わない。
     */
    private const int EXIT_CODE_DURATION_EXCEEDED = 10;

    public function __invoke(): void
    {
        foreach ($this->destinations() as $destination) {
            $this->copyTo($destination);
        }
    }

    /**
     * dropboxとlocal_backupを毎回固定順で実行すると、時間切れが続く間
     * 後者に一切進捗が回らなくなる。実行のたびに順序を入れ替え、両宛先に
     * 交互に転送機会を与える。
     *
     * @return array<int, string>
     */
    private function destinations(): array
    {
        $destinations = [
            config('rclone.uploads_remote'),
            config('rclone.uploads_local_backup'),
        ];

        if (now()->second % 2 === 1) {
            $destinations = array_reverse($destinations);
        }

        return $destinations;
    }

    private function copyTo(string $destination): void
    {
        $result = Process::timeout(self::TIMEOUT_SECONDS)->run([
            config('rclone.binary_path'),
            'copy',
            config('rclone.uploads_source'),
            $destination,
            '--max-duration', self::MAX_DURATION,
            '--cutoff-mode', 'SOFT',
        ]);

        if ($result->failed() && $result->exitCode() !== self::EXIT_CODE_DURATION_EXCEEDED) {
            throw new RuntimeException("rclone copy to {$destination} failed: ".$result->errorOutput());
        }
    }
}
