<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\article\AdvancedSearchRequest;
use App\Services\AdvancedSearchService;

class AdvancedSearchController extends Controller
{
    private AdvancedSearchService $advancedSearchService;

    public function __construct(
        AdvancedSearchService $advancedSearchService
    ) {
        $this->advancedSearchService = $advancedSearchService;
    }

    /**
     * 詳細検索結果一覧.
     */
    public function search(AdvancedSearchRequest $request)
    {
        $advancedSearch = $request->validated()['advancedSearch'] ?? [];

        $articles = $this->advancedSearchService->search($advancedSearch);
        $contents = [
            'articles' => $articles,
            'advancedSearch' => $advancedSearch,
            'options' => $this->advancedSearchService->getOptions(),
        ];

        return view('front.articles.index', $contents);
    }
}
