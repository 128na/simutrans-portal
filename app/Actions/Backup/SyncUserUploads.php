<?php

declare(strict_types=1);

namespace App\Actions\Backup;

use App\Exceptions\UploadBackupInterruptedException;
use Illuminate\Process\Exceptions\ProcessTimedOutException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Throwable;

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
     * 大きいファイル(最大300MB超)は25秒を大幅に超えて転送が続くことがある。
     * ここで先に強制終了すると--max-durationの意図(安全な自己終了)を潰してしまうため、
     * 明らかにハングした場合のみ止める保険として十分大きい値にする。
     */
    private const int TIMEOUT_SECONDS = 600;

    /**
     * rcloneが--max-durationで打ち切った際の終了コード。時間切れによる想定内の中断であり、失敗として扱わない。
     */
    private const int EXIT_CODE_DURATION_EXCEEDED = 10;

    public function __invoke(): int
    {
        $transferred = 0;
        $failures = [];
        $onlyInterrupted = true;
        $runId = (string) Str::uuid();

        foreach ($this->destinations() as $label => $destination) {
            try {
                $transferred += $this->copyTo($destination, $label, $runId);
            } catch (Throwable $throwable) {
                $failures[] = $label;
                $onlyInterrupted = $onlyInterrupted && $throwable instanceof UploadBackupInterruptedException;
            }
        }

        if ($failures !== []) {
            $message = 'Upload backup synchronization failed [run_id='.$runId.']: '.implode(', ', $failures);
            throw $onlyInterrupted ? new UploadBackupInterruptedException($message) : new RuntimeException($message);
        }

        return $transferred;
    }

    /**
     * dropboxとlocal_backupを毎回固定順で実行すると、時間切れが続く間
     * 後者に一切進捗が回らなくなる。実行のたびに順序を入れ替え、両宛先に
     * 交互に転送機会を与える。
     *
     * @return array<string, string>
     */
    private function destinations(): array
    {
        $destinations = [
            'dropbox' => config('rclone.uploads_remote'),
            'local_backup' => config('rclone.uploads_local_backup'),
        ];

        if (now()->second % 2 === 1) {
            $destinations = array_reverse($destinations, true);
        }

        return $destinations;
    }

    private function copyTo(string $destination, string $label, string $runId): int
    {
        $diagnostics = new RcloneDiagnostics;
        $started = hrtime(true);
        $context = ['run_id' => $runId, 'destination' => $label, 'started_at' => now()->toIso8601String(), 'pid' => null];
        $status = 'start_failed';
        $exitCode = null;
        $signal = null;
        $exceptionClass = null;
        $startedLogged = false;
        try {
            $process = Process::timeout(self::TIMEOUT_SECONDS)->quietly()->start([
                config('rclone.binary_path'),
                'copy',
                config('rclone.uploads_source'),
                $destination,
                '--max-duration', self::MAX_DURATION,
                '--cutoff-mode', 'SOFT',
                '-v',
                '--use-json-log',
                '--stats', '10s',
            ], $diagnostics->collect(...));
            $context['pid'] = $process->id();
            $this->log('started', $context);
            $startedLogged = true;
            $result = $process->wait();
            $exitCode = $result->exitCode();
            $status = $exitCode === 0 ? 'success' : ($exitCode === self::EXIT_CODE_DURATION_EXCEEDED ? 'duration_limit' : 'failed');
            if ($status === 'failed') {
                throw new RuntimeException('Backup destination failed.');
            }

            if ($status === 'success') {
                app(UploadBackupHealth::class)->recordSuccess($label);
            }

            return $diagnostics->transferred();
        } catch (ProcessTimedOutException $exception) {
            $exceptionClass = $exception::class;
            $exitCode = $exception->result->exitCode();
            $status = 'timeout';
            throw new RuntimeException('Backup destination timed out.');
        } catch (ProcessSignaledException $exception) {
            $exceptionClass = $exception::class;
            $signal = $exception->getSignal();
            $status = $signal === 9 ? 'sigkill' : 'signaled';
            if ($signal === 9) {
                throw new UploadBackupInterruptedException('Backup destination was interrupted.');
            }

            throw new RuntimeException('Backup destination was signaled.');
        } catch (Throwable $exception) {
            $exceptionClass = $exception::class;
            if ($startedLogged && $status === 'start_failed') {
                $status = 'failed';
            }
            throw new RuntimeException('Backup destination failed.');
        } finally {
            if (! $startedLogged) {
                $this->log('started', $context);
            }
            $this->log('finished', $context + [
                'elapsed_seconds' => (hrtime(true) - $started) / 1e9,
                'exit_code' => $exitCode,
                'signal' => $signal,
                'status' => $status,
                'exception_class' => $exceptionClass,
                'diagnostics' => $diagnostics->snapshot(),
            ]);
        }
    }

    /** @param array<string, mixed> $context */
    private function log(string $event, array $context): void
    {
        try {
            Log::channel('backup-sync')->info($event, $context);
        } catch (Throwable) {
        }
    }
}
