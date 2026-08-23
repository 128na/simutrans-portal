<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a .pak file cannot be parsed
 */
class InvalidPakFileException extends Exception
{
    public static function invalidHeader(): self
    {
        return new self('Invalid pak file header: magic string not found');
    }

    public static function corruptedNode(string $details = ''): self
    {
        $message = 'Corrupted node structure in pak file';
        if ($details !== '') {
            $message .= ': '.$details;
        }

        return new self($message);
    }

    public static function unexpectedEof(): self
    {
        return new self('Unexpected end of file while parsing pak file');
    }

    public static function unsupportedVersion(int $version): self
    {
        return new self('Unsupported pak file version: '.$version);
    }

    /**
     * Thrown by a TypeParser when an object node's own version field is not
     * one this parser implements (either newer than the highest known
     * version, or an old/legacy format the parser doesn't handle).
     *
     * Callers should treat this as a per-object, not per-file, failure:
     * catch it, log it, and keep the object's name/copyright/objectType
     * while dropping only its type-specific data.
     */
    public static function unsupportedTypeVersion(string $objectType, int $version, int $maxSupportedVersion): self
    {
        return new self(sprintf(
            'Unsupported %s version: %d (max known: %d). The pak compiler may be newer than this tool supports.',
            $objectType,
            $version,
            $maxSupportedVersion
        ));
    }
}
