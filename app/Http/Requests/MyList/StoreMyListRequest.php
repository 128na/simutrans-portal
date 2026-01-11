<?php

declare(strict_types=1);

namespace App\Http\Requests\MyList;

use Illuminate\Foundation\Http\FormRequest;

class StoreMyListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1', 'max:120'],
            'note' => ['nullable', 'string', 'max:65535'],
            'is_public' => ['boolean'],
        ];
    }
}
