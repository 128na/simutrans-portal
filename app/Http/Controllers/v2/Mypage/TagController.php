<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2\Mypage;

use App\Http\Requests\Api\Tag\StoreRequest;
use App\Http\Requests\Api\Tag\UpdateRequest;
use App\Http\Resources\v2\Tag as MypageTag;
use App\Models\Tag;
use App\Repositories\v2\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class TagController extends Controller
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.tags', [
            'tags' => MypageTag::collection($this->tagRepository->getForEdit()),
            'meta' => $this->metaOgpService->tags(),
        ]);
    }

    public function store(StoreRequest $storeRequest): MypageTag
    {
        $tag = $this->tagRepository->store([
            'name' => $storeRequest->input('name'),
            'description' => $storeRequest->input('description'),
            'created_by' => Auth::id(),
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);

        return new MypageTag($this->tagRepository->load($tag));
    }

    public function update(Tag $tag, UpdateRequest $updateRequest): MypageTag
    {
        $old = $tag->description;
        if (Auth::user()->cannot('update', $tag)) {
            return abort(403);
        }
        $tag = $this->tagRepository->update($tag, [
            'description' => $updateRequest->input('description'),
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);
        event(new \App\Events\Tag\TagDescriptionUpdated($tag, Auth::user(), $old));
        return new MypageTag($this->tagRepository->load($tag));
    }
}
