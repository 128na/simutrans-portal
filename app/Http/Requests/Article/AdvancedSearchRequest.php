<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class AdvancedSearchRequest extends FormRequest
{
    public function rules()
    {
        return [
            'advancedSearch.word' => 'nullable|string|max:100',
            'advancedSearch.categoryIds.*' => 'nullable|exists:categories,id',
            'advancedSearch.categoryAnd' => 'nullable|bool',
            'advancedSearch.tagIds.*' => 'nullable|exists:tags,id',
            'advancedSearch.tagAnd' => 'nullable|bool',
            'advancedSearch.userIds.*' => 'nullable|exists:users,id',
            'advancedSearch.userAnd' => 'nullable|bool',
            'advancedSearch.startAt' => 'nullable|date',
            'advancedSearch.endAt' => 'nullable|date|after:advancedSearch.startAt',
            'advancedSearch.order' => 'nullable|in:created_at,updated_at,title',
            'advancedSearch.direction' => 'nullable|in:desc,asc',
        ];
    }
}
