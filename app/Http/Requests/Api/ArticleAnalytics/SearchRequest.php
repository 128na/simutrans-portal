<?php

namespace App\Http\Requests\Api\ArticleAnalytics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user_id = Auth::id();

        return [
            'ids' => 'required|array|max:10',
            'ids.*' => "required|exists:articles,id,user_id,{$user_id}",
            'type' => 'required|in:daily,monthly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }
}
