<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\ArticleAnalytics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

final class SearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'ids' => ['required', 'array', 'max:50'],
            'ids.*' => 'required|exists:articles,id,user_id,'.$userId,
            'type' => ['required', 'in:daily,monthly,yearly'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }
}
