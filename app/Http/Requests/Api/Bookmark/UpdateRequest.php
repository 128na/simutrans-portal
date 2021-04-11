<?php

namespace App\Http\Requests\Api\Bookmark;

use App\Models\User\BookmarkItem;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        $idRule = function (string $attribute, string $value, Closure $fail) {
            $typeAttr = str_replace('bookmark_itemable_id', 'bookmark_itemable_type', $attribute);
            $type = $this->input($typeAttr);

            if ($type::where('id', $value)->doesntExist()) {
                $fail('選択された:attributeは正しくありません。');
            }
        };

        return [
            'bookmark.title' => 'required|string|max:255',
            'bookmark.description' => 'nullable|string|max:1000',
            'bookmark.is_public' => 'nullable|bool',

            'bookmarkItems.*.bookmark_itemable_type' => ['required', Rule::in(BookmarkItem::BOOKMARK_ITEMABLE_TYPES), 'bail'],
            'bookmarkItems.*.bookmark_itemable_id' => ['required', $idRule],
            'bookmarkItems.*.memo' => 'nullable|max:1000',
            'bookmarkItems.*.order' => 'nullable|integer',
        ];
    }
}
