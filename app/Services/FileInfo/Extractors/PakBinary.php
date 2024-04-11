<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors;

final class PakBinary
{
    private int $position = 0;

    public function __construct(
        private readonly string $binary,
    ) {
    }

    public function readChar(int $bytes): string
    {
        $chars = mb_substr($this->binary, $this->position, $bytes);
        $this->position += $bytes;

        return $chars;
    }

    public function get(): string
    {
        return mb_substr($this->binary, $this->position);
    }

    public function seek(int $bytes): void
    {
        $this->position += $bytes;
    }

    public function eof(): bool
    {
        return $this->position >= mb_strlen($this->binary);
    }

    public function seekUntil(string $target): int
    {
        $pos = mb_stripos($this->get(), $target);

        if ($pos) {
            $this->position += $pos;
        } else {
            $this->position = mb_strlen($this->binary);
        }

        return $this->position;
    }
}
