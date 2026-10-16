<?php

declare(strict_types=1);

namespace App\Http\Requests\MyList;

use Illuminate\Foundation\Http\FormRequest;

class ListPublicMyListsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:updated_at,updated_at:asc,updated_at:desc,created_at,created_at:asc,created_at:desc,title,title:asc,title:desc'],
        ];
    }
}
