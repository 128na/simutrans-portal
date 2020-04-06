<?php

namespace App\Http\Controllers\Api\v2\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\SearchRequest;
use App\Http\Requests\Api\Tag\StoreRequest;
use App\Services\TagService;

class TagController extends Controller
{
    /**
     * @var TagService
     */
    private $tag_service;
    //
    public function __construct(TagService $tag_service)
    {
        $this->tag_service = $tag_service;
    }

    public function search(SearchRequest $request)
    {
        return $this->tag_service->search($request->name);
    }

    public function store(StoreRequest $request)
    {
        return $this->tag_service->create([
            'name' => $request->name,
        ]);
    }
}
