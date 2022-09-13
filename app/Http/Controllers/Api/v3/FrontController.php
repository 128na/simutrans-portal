<?php

namespace App\Http\Controllers\Api\v3;

use App\Events\ArticleShown;
use App\Http\Controllers\Controller;
use App\Http\Resources\Front\ArticleResource;
use App\Http\Resources\Front\AttachmentResource;
use App\Http\Resources\Front\PakAddonResource;
use App\Http\Resources\Front\UserAddonResource;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Services\Front\SidebarService;
use Auth;

class FrontController extends Controller
{
    public function __construct(
        private SidebarService $sidebarService,
        private ArticleRepository $articleRepository,
    ) {
    }

    public function sidebar()
    {
        return [
            'user_addon_counts' => new UserAddonResource($this->sidebarService->userAddonCounts()),
            'pak_addon_counts' => new PakAddonResource($this->sidebarService->pakAddonsCounts()),
        ];
    }

    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        $isOwner = Auth::check() && Auth::user()->can('update', $article);
        if (!$isOwner) {
            event(new ArticleShown($article));
            $contents['gtag'] = $article->user->profile->data->gtag;
        }

        $article = $this->articleRepository->loadArticle($article);

        return [
            'article' => new ArticleResource($article),
            'attachments' => new AttachmentResource($article->attachments),
        ];
    }
}
