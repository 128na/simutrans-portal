<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Events\Tag\TagDescriptionUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\SearchRequest;
use App\Http\Requests\Api\Tag\StoreRequest;
use App\Http\Requests\Api\Tag\UpdateRequest;
use App\Http\Resources\Api\Mypage\Tag as ResourcesTag;
use App\Http\Resources\Api\Mypage\Tags;
use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\Auth;

final class TagController extends Controller
{
    public function __construct(
        private readonly TagRepository $tagRepository,
    ) {}

    public function search(SearchRequest $searchRequest): Tags
    {
        return new Tags($searchRequest->name
            ? $this->tagRepository->searchTags((string) $searchRequest->string('name'))
            : $this->tagRepository->getTags());
    }

    public function store(StoreRequest $storeRequest): ResourcesTag
    {
        $tag = $this->tagRepository->store([
            'name' => $storeRequest->name,
            'created_by' => Auth::id(),
        ]);

        return new ResourcesTag($tag);
    }

    public function update(Tag $tag, UpdateRequest $updateRequest): void
    {
        $old = $tag->description;
        $this->authorize('update', $tag);
        $this->tagRepository->update($tag, [
            'description' => $updateRequest->input('description'),
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);
        TagDescriptionUpdated::dispatch($tag, $this->loggedinUser(), $old);
    }
}
