<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Tag;

class StoreRequest extends SearchRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:20|unique:tags,name',
        ];
    }
}
