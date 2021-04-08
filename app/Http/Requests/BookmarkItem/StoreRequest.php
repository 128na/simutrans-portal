<?php

namespace App\Http\Requests\BookmarkItem;

use App\Models\User\BookmarkItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        $type = $this->input('bookmarkItem.bookmark_itemable_type');

        return [
            'bookmarkItem.bookmark_itemable_type' => ['required', Rule::in(BookmarkItem::BOOKMARK_ITEMABLE_TYPES), 'bail'],
            'bookmarkItem.bookmark_itemable_id' => ['required', "exists:{$type},id"],
            'bookmarkItem.memo' => 'nullable|max:1000',
        ];
    }
}
