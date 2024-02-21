<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Article;

use App\Http\Requests\Article\SearchRequest as BaseRequest;

class SearchRequest extends BaseRequest
{
    /**
     * @return array<mixed>
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'order' => 'nullable|in:published_at,modified_at',
            ]
        );
    }
}
