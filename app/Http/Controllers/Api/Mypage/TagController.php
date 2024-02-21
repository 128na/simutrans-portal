<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Events\Tag\TagDescriptionUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Tag\SearchRequest;
use App\Http\Requests\Api\Tag\StoreRequest;
use App\Http\Requests\Api\Tag\UpdateRequest;
use App\Http\Resources\Api\Mypage\Tag;
use App\Http\Resources\Api\Mypage\Tags;
use App\Models\Tag as ModelsTag;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function __construct(
        private readonly TagRepository $tagRepository,
    ) {
    }

    public function search(SearchRequest $searchRequest): Tags
    {
        return new Tags($searchRequest->name
            ? $this->tagRepository->searchTags($searchRequest->name)
            : $this->tagRepository->getTags());
    }

    public function store(StoreRequest $storeRequest): Tag
    {
        $model = $this->tagRepository->store([
            'name' => $storeRequest->name,
            'created_by' => Auth::id(),
        ]);

        return new Tag($model);
    }

    public function update(ModelsTag $modelsTag, UpdateRequest $updateRequest): void
    {
        $old = $modelsTag->description;
        $this->authorize('update', $modelsTag);
        $this->tagRepository->update($modelsTag, [
            'description' => $updateRequest->input('description'),
            'last_modified_by' => Auth::id(),
            'last_modified_at' => now(),
        ]);
        event(new TagDescriptionUpdated($modelsTag, $this->loggedinUser(), $old));
    }
}
