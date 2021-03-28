<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\SearchRequest;
use App\Http\Requests\Api\Tag\StoreRequest;
use App\Repositories\TagRepository;

class TagController extends Controller
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function search(SearchRequest $request)
    {
        return $request->name
            ? $this->tagRepository->searchTags($request->name)
            : $this->tagRepository->getTags();
    }

    public function store(StoreRequest $request)
    {
        return $this->tagRepository->store([
            'name' => $request->name,
        ]);
    }
}
