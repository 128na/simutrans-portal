<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class NgWordRule implements ValidationRule
{
    /**
     * @var array<string>
     */
    private array $detected = [];

    /**
     * Create a new rule instance.
     *
     * @param  array<string>  $ngWords
     * @return void
     */
    public function __construct(private readonly array $ngWords = [])
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->ngWords as $ngWord) {
            if (Str::contains($value, $ngWord)) {
                $this->detected[] = $ngWord;
            }
        }

        if (empty($this->detected)) {
            return;
        }
        $fail(sprintf(':attribute に使用できない文字が含まれています。(%s)', implode(',', $this->detected)));
    }
}
