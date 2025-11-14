<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Http\Requests\Tag\StoreRequest;
use App\Http\Requests\Tag\UpdateRequest;
use App\Http\Resources\TagEdit;
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
            'tags' => TagEdit::collection($this->tagRepository->getForEdit()),
            'meta' => $this->metaOgpService->tags(),
        ]);
    }

    public function store(StoreRequest $storeRequest): TagEdit
    {
        $tag = $this->tagRepository->store([
            'name' => $storeRequest->input('name'),
            'description' => $storeRequest->input('description'),
            'created_by' => Auth::id(),
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);

        return new TagEdit($this->tagRepository->load($tag));
    }

    public function update(Tag $tag, UpdateRequest $updateRequest): TagEdit
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

        return new TagEdit($this->tagRepository->load($tag));
    }
}
