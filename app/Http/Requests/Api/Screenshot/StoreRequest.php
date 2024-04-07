<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Screenshot;

use App\Enums\ScreenshotStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'screenshot.title' => 'required|string|max:256',
            'screenshot.description' => 'required|string|max:2048',
            'screenshot.status' => ['required', Rule::enum(ScreenshotStatus::class)],
            'screenshot.attachments' => 'required|array|min:1|max:10',
            'screenshot.attachments.*.id' => 'required|exists:attachments,id',
            'screenshot.attachments.*.order' => 'present|numeric',
            'screenshot.attachments.*.caption' => 'present|max:50',
            'screenshot.links' => 'present|array|max:10',
            'screenshot.links.*' => 'url|distinct',
            'screenshot.articles' => 'present|array|max:10',
            'screenshot.articles.*.id' => 'required|distinct|exists:articles,id,status,publish',
            'should_notify' => 'nullable|boolean',
        ];
    }
}
