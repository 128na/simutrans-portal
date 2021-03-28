<?php

namespace App\Services;

use App\Http\Requests\Api\Article\BaseRequest;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;

class ArticleEditorService extends Service
{
    private ArticleRepository $articleRepository;
    private CategoryRepository $categoryRepository;
    private TagRepository $tagRepository;

    public function __construct(
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        TagRepository $tagRepository
    ) {
        $this->articleRepository = $articleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->tagRepository = $tagRepository;
    }

    public function getArticles(User $user)
    {
        return $this->articleRepository->finaAllByUser($user);
    }

    public function getOptions(User $user)
    {
        return [
            'categories' => $this->getSeparatedCategories($user),
            'statuses' => $this->getStatuses(),
            'post_types' => $this->getPostTypes(),
        ];
    }

    public function getSeparatedCategories(User $user)
    {
        $categories = $this->categoryRepository->finaAllByUser($user);

        return $this->separateCategories($categories);
    }

    /**
     * タイプ別に分類したカテゴリ一覧を返す.
     */
    private function separateCategories($categories)
    {
        return collect($categories->reduce(function ($list, $item) {
            if (!isset($list[$item->type])) {
                $list[$item->type] = [];
            }
            $list[$item->type][] = [
                'text' => __("category.{$item->type}.{$item->slug}"),
                'value' => $item->id,
            ];

            return $list;
        }, []));
    }

    public function getStatuses()
    {
        return collect(config('status'))->map(
            fn ($item) => [
                'text' => __("statuses.{$item}"),
                'value' => $item,
            ]
        )->values();
    }

    public function getPostTypes()
    {
        return collect(config('post_types'))->map(
            fn ($item) => [
                'text' => __("post_types.{$item}"),
                'value' => $item,
            ]
        )->values();
    }

    public function storeArticle(User $user, StoreRequest $request)
    {
        $data = [
            'post_type' => $request->input('article.post_type'),
            'title' => $request->input('article.title'),
            'slug' => $request->input('article.slug'),
            'status' => $request->input('article.status'),
            'contents' => $request->input('article.contents'),
        ];
        $article = $this->articleRepository->storeByUser($user, $data);

        $this->syncRelated($article, $request);

        return $article->fresh();
    }

    public function updateArticle(Article $article, UpdateRequest $request)
    {
        $this->articleRepository->update($article, [
            'title' => $request->input('article.title'),
            'slug' => $request->input('article.slug'),
            'status' => $request->input('article.status'),
            'contents' => $request->input('article.contents'),
        ]);

        $this->syncRelated($article, $request);

        return $article->fresh();
    }

    private function syncRelated(Article $article, BaseRequest $request)
    {
        // 添付
        $attachmentIds = collect([
            $request->input('article.contents.thumbnail'),
            $request->input('article.contents.file'),
        ])
            ->merge($request->input('article.contents.sections.*.id', []))
            ->filter()
            ->toArray();
        $this->articleRepository->syncAttachments($article, $attachmentIds);

        // カテゴリ
        $this->articleRepository->syncCategories($article, $request->input('article.categories', []));

        // タグ
        $tagIds = $this->tagRepository->getIdsByNames($request->input('article.tags', []));
        $this->articleRepository->syncTags($article, $tagIds->toArray());
    }
}
