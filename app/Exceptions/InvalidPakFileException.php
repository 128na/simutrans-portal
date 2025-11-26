<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a .pak file cannot be parsed
 */
final class InvalidPakFileException extends Exception
{
    public static function invalidHeader(): self
    {
        return new self('Invalid pak file header: magic string not found');
    }

    public static function corruptedNode(string $details = ''): self
    {
        $message = 'Corrupted node structure in pak file';
        if ($details !== '') {
            $message .= ': ' . $details;
        }

        return new self($message);
    }

    public static function unexpectedEof(): self
    {
        return new self('Unexpected end of file while parsing pak file');
    }

    public static function unsupportedVersion(int $version): self
    {
        return new self("Unsupported pak file version: {$version}");
    }
}
