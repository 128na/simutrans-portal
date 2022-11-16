<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag as ModelsTag;
use App\Repositories\TagRepository;

class TagController extends Controller
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function toggleEditable(ModelsTag $tag)
    {
        $this->tagRepository->update($tag, [
            'editable' => !$tag->editable,
        ]);
    }
}
