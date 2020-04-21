<?php
namespace App\Services;

use App\Http\Requests\Api\Article\BaseRequest;
use App\Http\Requests\Api\Article\StoreRequest;
use App\Http\Requests\Api\Article\UpdateRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;

class ArticleEditorService extends Service
{
    /**
     * @var Category
     */
    private $category;
    /**
     * @var Tag
     */
    private $tag;

    public function __construct(Category $category, Tag $tag)
    {
        $this->category = $category;
        $this->tag = $tag;
    }

    public function getArticles(User $user)
    {
        return $user->articles()
            ->with('categories', 'tags', 'totalViewCount', 'totalConversionCount')
            ->get();
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
        $categories = $this->category->forUser($user)->get();
        return self::separateCategories($categories);
    }

    /**
     * タイプ別に分類したカテゴリ一覧を返す
     */
    private static function separateCategories($categories)
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
        return collect(config('status'))->map(function ($item) {
            return [
                'text' => __("statuses.{$item}"),
                'value' => $item,
            ];
        })->values();
    }
    public function getPostTypes()
    {
        return collect(config('post_types'))->map(function ($item) {
            return [
                'text' => __("post_types.{$item}"),
                'value' => $item,
            ];
        })->values();
    }

    public function storeArticle(User $user, StoreRequest $request)
    {
        $article = $user->articles()->create([
            'post_type' => $request->input('article.post_type'),
            'title' => $request->input('article.title'),
            'slug' => $request->input('article.slug'),
            'status' => $request->input('article.status'),
            'contents' => $request->input('article.contents'),
        ]);

        $this->syncRelated($article, $request);

        return $article->fresh();
    }
    public function updateArticle(Article $article, UpdateRequest $request)
    {
        $article->update([
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
        $attachment_ids = collect([
            $request->input('article.contents.thumbnail'),
            $request->input('article.contents.file'),
        ])
            ->merge($request->input('article.contents.sections.*.id', []))
            ->filter();

        $article->attachments()->saveMany(
            $article->user->myAttachments()->find($attachment_ids)
        );

        $article->categories()->sync($request->input('article.categories', []));

        $tag_names = $request->input('article.tags', []);
        $tags_ids = $this->tag->whereIn('name', $tag_names)->get()->pluck('id')->toArray();
        $article->tags()->sync($tags_ids);

        $this->tag->removeDoesntHaveRelation();
    }
}
