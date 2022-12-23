<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class NgWordRule implements Rule
{
    private array $detected = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private array $ngWords = [])
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($this->ngWords as $ngWord) {
            if (Str::contains($value, $ngWord)) {
                $this->detected[] = $ngWord;
            }
        }

        return empty($this->detected);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return sprintf('使用できない文字が含まれています。(%s)', implode(',', $this->detected));
    }
}
