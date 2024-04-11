<?php

declare(strict_types=1);

namespace App\Rules;

use App\Repositories\AttachmentRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final readonly class ImageAttachment implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private readonly AttachmentRepository $attachmentRepository)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_int($value)) {
            $fail(':attribute が正しくありません)');

            return;
        }

        /**
         * @var \App\Models\Attachment|null
         */
        $attachment = $this->attachmentRepository->find($value);

        if ($attachment && $attachment->is_image) {
            return;
        }

        $tranlated_attribute = app('translator')->get('validation.attributes')[$attribute] ?? $attribute;
        $fail(__('validation.image', ['attribute' => $tranlated_attribute]));
    }
}
