<?php

namespace App\Rules;

use App\Repositories\AttachmentRepository;
use Illuminate\Contracts\Validation\Rule;

class ImageAttachment implements Rule
{
    private AttachmentRepository $attachmentRepository;
    private string $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(AttachmentRepository $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $attachment = $this->attachmentRepository->find($value);

        if ($attachment && $attachment->is_image) {
            return true;
        }
        $tranlated_attribute = app('translator')->get('validation.attributes')[$attribute] ?? $attribute;
        $this->message = __('validation.image', ['attribute' => $tranlated_attribute]);

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
