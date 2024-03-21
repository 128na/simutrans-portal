<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Screenshot;

use App\Enums\ScreenShotStatus;
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
            'screenshot.status' => ['required', Rule::in(ScreenShotStatus::cases())],
            'screenshot.attachments' => 'required|array|min:1|max:10',
            'screenshot.attachments.*' => 'required|exists:attachments,id',
            'screenshot.links' => 'present|array|max:10',
            'screenshot.links.*' => 'url|distinct',
            'screenshot.articles' => 'present|array|max:10',
            'screenshot.articles.*.id' => 'required|distinct|exists:articles,id,status,publish',
            'screenshot.extra' => 'present|array',
            'screenshot.extra.attachments.*.id' => 'present|numeric',
            'screenshot.extra.attachments.*.order' => 'present|numeric',
            'screenshot.extra.attachments.*.caption' => 'present|max:50',
        ];
    }
}
