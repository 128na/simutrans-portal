<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\SearchRequest;
use App\Http\Requests\Api\Tag\StoreRequest;
use App\Http\Requests\Api\Tag\UpdateRequest;
use App\Http\Resources\Api\Mypage\Tag;
use App\Http\Resources\Api\Mypage\Tags;
use App\Models\Tag as ModelsTag;
use App\Repositories\TagRepository;
use App\Services\Logging\AuditLogService;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function __construct(
        private TagRepository $tagRepository,
        private AuditLogService $auditLogService,
    ) {
    }

    public function search(SearchRequest $request): Tags
    {
        return new Tags($request->name
            ? $this->tagRepository->searchTags($request->name)
            : $this->tagRepository->getTags());
    }

    public function store(StoreRequest $request): Tag
    {
        $tag = $this->tagRepository->store([
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        return new Tag($tag);
    }

    public function update(ModelsTag $tag, UpdateRequest $request): void
    {
        $old = $tag->description;
        $this->authorize('update', $tag);
        $this->tagRepository->update($tag, [
            'description' => $request->input('description'),
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);
        $this->auditLogService->tagDescriptionUpdate($this->loggedinUser(), $tag, $old, $request->string('description') ?? '');
    }
}
