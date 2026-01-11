<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\InvalidPakFileException;
use Tests\Unit\TestCase;

class InvalidPakFileExceptionTest extends TestCase
{
    public function test_invalid_header_returns_correct_message(): void
    {
        $exception = InvalidPakFileException::invalidHeader();

        $this->assertInstanceOf(InvalidPakFileException::class, $exception);
        $this->assertEquals('Invalid pak file header: magic string not found', $exception->getMessage());
    }

    public function test_corrupted_node_without_details(): void
    {
        $exception = InvalidPakFileException::corruptedNode();

        $this->assertInstanceOf(InvalidPakFileException::class, $exception);
        $this->assertEquals('Corrupted node structure in pak file', $exception->getMessage());
    }

    public function test_corrupted_node_with_details(): void
    {
        $details = 'Missing required field: name';
        $exception = InvalidPakFileException::corruptedNode($details);

        $this->assertInstanceOf(InvalidPakFileException::class, $exception);
        $this->assertEquals('Corrupted node structure in pak file: Missing required field: name', $exception->getMessage());
    }

    public function test_unexpected_eof_returns_correct_message(): void
    {
        $exception = InvalidPakFileException::unexpectedEof();

        $this->assertInstanceOf(InvalidPakFileException::class, $exception);
        $this->assertEquals('Unexpected end of file while parsing pak file', $exception->getMessage());
    }

    public function test_unsupported_version_returns_correct_message(): void
    {
        $version = 999;
        $exception = InvalidPakFileException::unsupportedVersion($version);

        $this->assertInstanceOf(InvalidPakFileException::class, $exception);
        $this->assertEquals('Unsupported pak file version: 999', $exception->getMessage());
    }
}
