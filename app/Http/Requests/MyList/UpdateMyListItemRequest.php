<?php

declare(strict_types=1);

namespace App\Http\Requests\MyList;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMyListItemRequest extends FormRequest
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
            'note' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
