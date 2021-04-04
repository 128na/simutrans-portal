<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\article\AdvancedSearchRequest;
use App\Services\AdvancedSearchservice;

class AdvancedSearchController extends Controller
{
    private AdvancedSearchservice $advancedSearchservice;

    public function __construct(
        AdvancedSearchservice $advancedSearchservice
    ) {
        $this->advancedSearchservice = $advancedSearchservice;
    }

    /**
     * 詳細検索結果一覧.
     */
    public function search(AdvancedSearchRequest $request)
    {
        $advancedSearch = $request->validated()['advancedSearch'] ?? [];

        $articles = $this->advancedSearchservice->search($advancedSearch);
        $contents = [
            'articles' => $articles,
            'advancedSearch' => $advancedSearch,
        ];

        return view('front.articles.advancedSearch', $contents);
    }
}
