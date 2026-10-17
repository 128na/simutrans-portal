<?php

declare(strict_types=1);

namespace App\Actions\Backup;

class RcloneDiagnostics
{
    private const int BUFFER_LIMIT = 16384;

    /** @var array<string, string> */
    private array $buffers = ['out' => '', 'err' => ''];

    /** @var array<string, bool> */
    private array $discarding = ['out' => false, 'err' => false];

    /** @var array<string, mixed> */
    private array $snapshot = [];

    private int $transferred = 0;

    public function collect(string $type, string $chunk): void
    {
        $stream = in_array($type, ['out', 'stdout'], true) ? 'out' : 'err';
        $offset = 0;
        $length = strlen($chunk);
        while ($offset < $length) {
            $newline = strpos($chunk, "\n", $offset);
            $end = $newline === false ? $length : $newline;
            $partLength = $end - $offset;
            if (! $this->discarding[$stream]) {
                if ($partLength > self::BUFFER_LIMIT - strlen($this->buffers[$stream])) {
                    $this->buffers[$stream] = '';
                    $this->discarding[$stream] = true;
                } else {
                    $this->buffers[$stream] .= substr($chunk, $offset, $partLength);
                }
            }
            if ($newline !== false) {
                if (! $this->discarding[$stream]) {
                    $this->parse($this->buffers[$stream]);
                }
                $this->buffers[$stream] = '';
                $this->discarding[$stream] = false;
            }
            $offset = $end + 1;
        }
    }

    /** @return array<string, mixed> */
    public function snapshot(): array
    {
        return $this->snapshot;
    }

    public function transferred(): int
    {
        return $this->transferred;
    }

    private function parse(string $line): void
    {
        $record = json_decode($line, true, 16);
        if (! is_array($record)) {
            return;
        }
        if (isset($record['level']) && in_array($record['level'], ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'], true)) {
            $this->snapshot['last_level'] = $record['level'];
        }
        if (is_string($record['object'] ?? null)) {
            $this->snapshot['object'] = $this->identifier($record['object']);
        }
        if (! is_array($record['stats'] ?? null)) {
            return;
        }
        $stats = $this->numbers($record['stats'], ['bytes', 'totalBytes', 'checks', 'totalChecks', 'transfers', 'totalTransfers', 'errors', 'speed', 'elapsedTime', 'eta']);
        if ($stats !== []) {
            $stats['transferring'] = [];
        }
        if (is_int($stats['transfers'] ?? null)) {
            $this->transferred = $stats['transfers'];
        }
        if (is_array($record['stats']['transferring'] ?? null)) {
            $transferring = [];
            foreach (array_slice($record['stats']['transferring'], 0, 4) as $transfer) {
                if (is_array($transfer) && is_string($transfer['name'] ?? null)) {
                    $transferring[] = ['name' => $this->identifier($transfer['name'])] + $this->numbers($transfer, ['size', 'bytes', 'speed', 'percentage']);
                }
            }
            $stats['transferring'] = $transferring;
        }
        $this->snapshot['stats'] = array_replace($this->snapshot['stats'] ?? [], $stats);
    }

    /**
     * @param  array<string, mixed>  $values
     * @param  list<string>  $keys
     * @return array<string, int|float>
     */
    private function numbers(array $values, array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $value = $values[$key] ?? null;
            if ((is_int($value) || is_float($value)) && is_finite((float) $value) && $value >= 0 && $value <= PHP_INT_MAX) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function identifier(string $name): string
    {
        if (preg_match('~^(?:user/)?[0-9]{1,20}/[A-Za-z0-9]{40}\.[A-Za-z0-9]{1,10}$~D', $name) === 1) {
            return $name;
        }

        return 'sha256:'.hash('sha256', $name);
    }
}
