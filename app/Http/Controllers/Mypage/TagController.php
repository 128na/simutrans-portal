<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Events\Tag\TagDescriptionUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\StoreRequest;
use App\Http\Requests\Tag\UpdateRequest;
use App\Http\Resources\Mypage\TagEdit;
use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        return view('mypage.tags', [
            'tags' => TagEdit::collection($this->tagRepository->getForEdit()),
            'meta' => $this->metaOgpService->mypageTags(),
        ]);
    }

    /**
     * 新しいタグを作成
     */
    public function store(StoreRequest $storeRequest): TagEdit
    {
        /** @var int $userId */
        $userId = Auth::id();

        $tag = $this->tagRepository->store([
            'name' => $storeRequest->string('name')->value(),
            'description' => $storeRequest->filled('description') ? $storeRequest->string('description')->value() : null,
            'created_by' => $userId,
            'last_modified_by' => $userId,
            'last_modified_at' => now(),
        ]);

        return new TagEdit($this->tagRepository->load($tag));
    }

    /**
     * タグを更新
     */
    public function update(Tag $tag, UpdateRequest $updateRequest): TagEdit
    {
        $old = $tag->description;
        $this->authorize('update', $tag);
        $user = $this->loggedinUser();

        /** @var int $userId */
        $userId = Auth::id();

        $tag = $this->tagRepository->update($tag, [
            'description' => $updateRequest->filled('description') ? $updateRequest->string('description')->value() : null,
            'last_modified_by' => $userId,
            'last_modified_at' => now(),
        ]);
        event(new TagDescriptionUpdated($tag, $user, $old));

        return new TagEdit($this->tagRepository->load($tag));
    }
}
