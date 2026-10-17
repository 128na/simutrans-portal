<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class SafeAttachment implements ValidationRule
{
    /** @var list<string> */
    public const ALLOWED_EXTENSIONS = [
        'png', 'jpg', 'jpeg', 'jfif', 'ppm',
        'zip', '7z', 'pak', 'tab', 'txt', 'pdf',
    ];

    /** @var list<string> */
    private const UNSAFE_MIME_TYPES = [
        'text/html', 'application/xhtml+xml', 'image/svg+xml',
        'text/x-php', 'application/x-php', 'application/x-httpd-php',
        'text/javascript', 'application/javascript', 'application/x-javascript',
        'application/x-executable', 'application/x-pie-executable',
        'application/x-sharedlib', 'application/x-dosexec',
        'application/x-msdownload', 'application/vnd.microsoft.portable-executable',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('validation.file')->translate();

            return;
        }

        if (! in_array(strtolower($value->getClientOriginalExtension()), self::ALLOWED_EXTENSIONS, true)
            || in_array($value->getMimeType(), self::UNSAFE_MIME_TYPES, true)) {
            $fail('validation.mimes')->translate(['values' => implode(', ', self::ALLOWED_EXTENSIONS)]);
        }
    }
}
