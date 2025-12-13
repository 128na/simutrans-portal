<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SluggableString implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    #[\Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(':attribute に使用できない文字が含まれています。(英数字、-、_以外)');

            return;
        }

        $matched = preg_match('/\A[\w\d\-_]+\z/', $value, $matches);
        if ($matched !== 1) {
            $fail(':attribute に使用できない文字が含まれています。(英数字、-、_以外)');
        }
    }
}
