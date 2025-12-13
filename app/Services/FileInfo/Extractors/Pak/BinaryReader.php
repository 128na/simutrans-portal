<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak;

use App\Exceptions\InvalidPakFileException;
use OutOfBoundsException;

/**
 * Binary reader for little-endian data
 */
class BinaryReader
{
    private int $position = 0;

    public function __construct(
        private readonly string $binary,
    ) {}

    public function readUint8(): int
    {
        if (! $this->hasMore(1)) {
            throw InvalidPakFileException::unexpectedEof();
        }

        $result = unpack('C', substr($this->binary, $this->position, 1));
        if ($result === false) {
            throw new OutOfBoundsException('Failed to read uint8');
        }

        $this->position += 1;

        return $result[1];
    }

    public function readSint8(): int
    {
        if (! $this->hasMore(1)) {
            throw InvalidPakFileException::unexpectedEof();
        }

        $result = unpack('c', substr($this->binary, $this->position, 1));
        if ($result === false) {
            throw new OutOfBoundsException('Failed to read sint8');
        }

        $this->position += 1;

        return $result[1];
    }

    public function readUint16LE(): int
    {
        if (! $this->hasMore(2)) {
            throw InvalidPakFileException::unexpectedEof();
        }

        $result = unpack('v', substr($this->binary, $this->position, 2));
        if ($result === false) {
            throw new OutOfBoundsException('Failed to read uint16');
        }

        $this->position += 2;

        return $result[1];
    }

    public function readUint32LE(): int
    {
        if (! $this->hasMore(4)) {
            throw InvalidPakFileException::unexpectedEof();
        }

        $result = unpack('V', substr($this->binary, $this->position, 4));
        if ($result === false) {
            throw new OutOfBoundsException('Failed to read uint32');
        }

        $this->position += 4;

        return $result[1];
    }

    public function readString(int $length): string
    {
        if (! $this->hasMore($length)) {
            throw InvalidPakFileException::unexpectedEof();
        }

        $result = substr($this->binary, $this->position, $length);
        $this->position += $length;

        return $result;
    }

    public function readNullTerminatedString(): string
    {
        $start = $this->position;
        $end = strpos($this->binary, "\0", $start);

        if ($end === false) {
            throw InvalidPakFileException::unexpectedEof();
        }

        $result = substr($this->binary, $start, $end - $start);
        $this->position = $end + 1;

        return $result;
    }

    public function skip(int $bytes): void
    {
        if (! $this->hasMore($bytes)) {
            throw InvalidPakFileException::unexpectedEof();
        }

        $this->position += $bytes;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function seek(int $position): void
    {
        if ($position < 0 || $position > strlen($this->binary)) {
            throw new OutOfBoundsException('Invalid seek position: '.$position);
        }

        $this->position = $position;
    }

    public function hasMore(int $bytes = 1): bool
    {
        return $this->position + $bytes <= strlen($this->binary);
    }

    public function getRemainingLength(): int
    {
        return strlen($this->binary) - $this->position;
    }
}
