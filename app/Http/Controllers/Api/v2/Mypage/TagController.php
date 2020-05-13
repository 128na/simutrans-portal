<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\SearchRequest;
use App\Http\Requests\Api\Tag\StoreRequest;
use App\Services\TagService;

class TagController extends Controller
{
    private TagService $tag_service;
    //
    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
    }

    public function search(SearchRequest $request)
    {
        return $request->name
        ? $this->tag_service->searchTags($request->name)
        : $this->tag_service->getTags();
    }

    public function store(StoreRequest $request)
    {
        $this->tag_service->create([
            'name' => $request->name,
        ]);
        return $this->tag_service->getTags();
    }
}
