<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag as ModelsTag;
use App\Repositories\TagRepository;

class TagController extends Controller
{
    public function __construct(private readonly TagRepository $tagRepository)
    {
    }

    public function toggleEditable(ModelsTag $tag): void
    {
        $this->tagRepository->update($tag, [
            'editable' => ! $tag->editable,
        ]);
    }
}
