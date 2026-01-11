<?php

declare(strict_types=1);

namespace App\Http\Requests\MyList;

use Illuminate\Foundation\Http\FormRequest;

class ReorderMyListItemsRequest extends FormRequest
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
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.position' => ['required', 'integer', 'min:1'],
        ];
    }
}
