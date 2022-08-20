<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\SearchRequest;
use App\Http\Requests\Api\Tag\StoreRequest;
use App\Http\Resources\Api\Mypage\Tag;
use App\Http\Resources\Api\Mypage\Tags;
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
        return new Tags($request->name
            ? $this->tagRepository->searchTags($request->name)
            : $this->tagRepository->getTags()
        );
    }

    public function store(StoreRequest $request)
    {
        $tag = $this->tagRepository->store([
            'name' => $request->name,
        ]);

        return new Tag($tag);
    }
}
