<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Backup;

use App\Actions\Backup\RcloneDiagnostics;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Tests\Unit\TestCase;

class RcloneDiagnosticsTest extends TestCase
{
    public function test_only_allowlisted_numbers_safe_ids_and_levels_survive(): void
    {
        $diagnostics = new RcloneDiagnostics;
        $safeName = '123/'.str_repeat('a', 40).'.zip';
        $diagnostics->collect('err', json_encode([
            'level' => 'error', 'msg' => 'secret-token', 'error' => 'secret-token', 'url' => 'https://secret-token',
            'object' => 'secret-token.zip',
            'stats' => [
                'bytes' => 30, 'totalBytes' => 90, 'checks' => 1, 'totalChecks' => 2, 'transfers' => 3, 'totalTransfers' => 4,
                'errors' => 0, 'speed' => 1.25, 'elapsedTime' => 2.0, 'eta' => 60, 'unknown' => 'secret-token',
                'transferring' => [
                    ['name' => $safeName, 'bytes' => 12, 'size' => 50, 'speed' => 2.5, 'percentage' => 24, 'url' => 'secret-token'],
                    ['name' => 'secret-token.zip', 'bytes' => 4],
                    ['name' => 'https://secret-token'], ['name' => 'private-name'], ['name' => 'fifth-name'],
                ],
            ],
        ], JSON_THROW_ON_ERROR)."\n");
        $snapshot = $diagnostics->snapshot();
        $this->assertSame('error', $snapshot['last_level']);
        $this->assertSame('sha256:'.hash('sha256', 'secret-token.zip'), $snapshot['object']);
        $this->assertCount(4, $snapshot['stats']['transferring']);
        $this->assertSame($safeName, $snapshot['stats']['transferring'][0]['name']);
        $this->assertSame(12, $snapshot['stats']['transferring'][0]['bytes']);
        $serialized = json_encode($snapshot, JSON_THROW_ON_ERROR);
        foreach (['secret-token', 'msg', '"error":', 'url', 'unknown', 'private-name', 'fifth-name'] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $serialized);
        }
        $this->assertSame(3, $diagnostics->transferred());
    }

    public function test_split_and_interleaved_streams_do_not_mix_and_incomplete_json_is_ignored(): void
    {
        $diagnostics = new RcloneDiagnostics;
        $diagnostics->collect('out', '{"stats":{"bytes":');
        $diagnostics->collect('err', "{\"stats\":{\"transfers\":2}}\n");
        $diagnostics->collect('out', "123}}\n");
        $diagnostics->collect('err', '{"stats":{"transfers":99}}');
        $this->assertSame(2, $diagnostics->transferred());
        $this->assertSame(123, $diagnostics->snapshot()['stats']['bytes']);
    }

    public function test_oversized_lines_are_discarded_until_newline_and_valid_following_records_survive(): void
    {
        $diagnostics = new RcloneDiagnostics;
        $diagnostics->collect('err', str_repeat('x', 1024 * 1024));
        $diagnostics->collect('out', str_repeat('y', 16384));
        $diagnostics->collect('out', 'overflow');
        $diagnostics->collect('err', "{\"stats\":{\"transfers\":99}}\n{\"stats\":{\"transfers\":4}}\n");
        $this->assertSame(4, $diagnostics->transferred());
        $diagnostics->collect('out', "\n{\"stats\":{\"bytes\":20}}\n");
        $this->assertSame(20, $diagnostics->snapshot()['stats']['bytes']);
        $buffers = (new \ReflectionProperty($diagnostics, 'buffers'))->getValue($diagnostics);
        foreach ($buffers as $buffer) {
            $this->assertLessThanOrEqual(16384, strlen($buffer));
        }
    }

    public function test_invalid_values_do_not_destroy_last_valid_stats_or_transfer_count(): void
    {
        $diagnostics = new RcloneDiagnostics;
        $diagnostics->collect('err', "{\"stats\":{\"transfers\":7,\"bytes\":30}}\n");
        $diagnostics->collect('err', "{\"stats\":{\"transfers\":\"secret-token\",\"bytes\":-1,\"speed\":1e999}}\n");
        $diagnostics->collect('err', "{\"level\":\"secret-token\",\"stats\":{\"transfers\":[],\"unknown\":\"secret-token\"}}\ninvalid\n");
        $this->assertSame(7, $diagnostics->transferred());
        $this->assertSame(30, $diagnostics->snapshot()['stats']['bytes']);
        $this->assertStringNotContainsString('secret-token', json_encode($diagnostics->snapshot(), JSON_THROW_ON_ERROR));
    }

    public function test_completed_stats_clear_previous_in_flight_file_identifiers(): void
    {
        $diagnostics = new RcloneDiagnostics;
        $diagnostics->collect('err', "{\"stats\":{\"transfers\":0,\"transferring\":[{\"name\":\"example.txt\",\"bytes\":10}]}}\n");
        $this->assertCount(1, $diagnostics->snapshot()['stats']['transferring']);
        $diagnostics->collect('err', "{\"stats\":{\"transfers\":1,\"bytes\":20}}\n");
        $this->assertSame([], $diagnostics->snapshot()['stats']['transferring']);
        $this->assertSame(1, $diagnostics->transferred());
    }

    public function test_real_quiet_async_process_delivers_json_callback_without_retaining_raw_output(): void
    {
        $diagnostics = new RcloneDiagnostics;
        $process = Process::timeout(10)->quietly()->start([
            PHP_BINARY, '-r', 'fwrite(STDERR, json_encode(["msg" => "secret-token", "stats" => ["transfers" => 3]])."\n"); usleep(100000); exit(10);',
        ], $diagnostics->collect(...));
        $pid = $process->id();
        $result = $process->wait();
        $this->assertIsInt($pid);
        $this->assertGreaterThan(0, $pid);
        $this->assertSame(10, $result->exitCode());
        $this->assertSame(3, $diagnostics->transferred());
        $this->assertSame('', $result->output());
        $this->assertSame('', $result->errorOutput());
        $this->assertStringNotContainsString('secret-token', json_encode($diagnostics->snapshot(), JSON_THROW_ON_ERROR));
    }

    #[RequiresOperatingSystemFamily('Linux')]
    public function test_real_signal_keeps_diagnostics_received_before_kill(): void
    {
        $diagnostics = new RcloneDiagnostics;
        $process = Process::timeout(15)->quietly()->start([
            PHP_BINARY, '-r', 'fwrite(STDERR, "{\"stats\":{\"bytes\":99}}\n"); fflush(STDERR); sleep(10);',
        ], $diagnostics->collect(...));
        $pid = $process->id();
        $deadline = hrtime(true) + 5_000_000_000;
        while (($diagnostics->snapshot()['stats']['bytes'] ?? null) !== 99 && hrtime(true) < $deadline && $process->running()) {
            usleep(10000);
        }
        $this->assertSame(99, $diagnostics->snapshot()['stats']['bytes'] ?? null);
        $process->signal(9);
        try {
            $process->wait();
            $this->fail('Expected signal termination.');
        } catch (ProcessSignaledException $exception) {
            $this->assertSame(9, $exception->getSignal());
        }
        $this->assertIsInt($pid);
        $this->assertSame(99, $diagnostics->snapshot()['stats']['bytes']);
    }
}
