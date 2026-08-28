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

    private const int TIMEOUT_SECONDS = 40;

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
     * @return array<int, string>
     */
    private function destinations(): array
    {
        return [
            config('rclone.uploads_remote'),
            config('rclone.uploads_local_backup'),
        ];
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
