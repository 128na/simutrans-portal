<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ArticleCard;
use App\Models\Tag;
use App\Repositories\Article\FrontArticleRepository;
use App\Repositories\TagRepository;
use App\Services\Front\ArticleListFilter;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function __construct(
        private readonly FrontArticleRepository $articleRepository,
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

    public function tag(Request $request, Tag $tag): View
    {
        $filter = ArticleListFilter::fromRequest($request);

        return view('pages.tags.show', [
            'tag' => $tag,
            'articles' => ArticleCard::collection($this->articleRepository->getByTag($tag->id, filter: $filter)),
            'filter' => $filter,
            'meta' => $this->metaOgpService->frontTag($tag),
        ]);
    }
}
