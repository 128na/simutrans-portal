<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Resources\Frontend\ArticleList;
use App\Models\Tag;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class TagController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function tags(): View
    {
        return view('pages.tags.index', [
            'tags' => $this->tagRepository->getForList(),
            'meta' => $this->metaOgpService->frontTags(),
        ]);
    }

    public function tag(Tag $tag): View
    {
        return view('pages.tags.show', [
            'tag' => $tag,
            'articles' => ArticleList::collection($this->articleRepository->getByTag($tag->id)),
            'meta' => $this->metaOgpService->frontTag($tag),
        ]);
    }
}
