<?php

namespace App\Http\Requests\Api\Tag;

class StoreRequest extends SearchRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:20|unique:tags,name',
        ];
    }
}
