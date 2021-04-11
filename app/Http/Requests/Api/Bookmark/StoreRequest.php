<?php

namespace App\Http\Requests\Api\Bookmark;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'bookmark.title' => 'required|string|max:255',
            'bookmark.description' => 'nullable|string|max:1000',
            'bookmark.is_public' => 'nullable|bool',
        ];
    }
}
