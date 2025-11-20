<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Models\Tag;
use App\Repositories\ArticleRepository;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class TagController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function tags(): View
    {
        return view('v2.tags.index', [
            'tags' => $this->tagRepository->getForList(),
            'meta' => $this->metaOgpService->frontTags(),
        ]);
    }

    public function tag(Tag $tag): View
    {
        return view('v2.tags.show', [
            'tag' => $tag,
            'articles' => $this->articleRepository->getByTag($tag->id),
            'meta' => $this->metaOgpService->frontTag($tag),
        ]);
    }
}
