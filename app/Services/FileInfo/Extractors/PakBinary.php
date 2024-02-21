<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors;

class PakBinary
{
    private int $position = 0;

    public function __construct(
        private string $binary,
    ) {
    }

    public function readChar(int $bytes): string
    {
        $chars = substr($this->binary, $this->position, $bytes);
        $this->position += $bytes;

        return $chars;
    }

    public function get(): string
    {
        return substr($this->binary, $this->position);
    }

    public function seek(int $bytes): void
    {
        $this->position += $bytes;
    }

    public function eof(): bool
    {
        return $this->position >= strlen($this->binary);
    }

    public function seekUntil(string $target): int
    {
        $pos = stripos($this->get(), $target);

        if ($pos) {
            $this->position += $pos;
        } else {
            $this->position = strlen($this->binary);
        }

        return $this->position;
    }
}
