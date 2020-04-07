<?php
namespace App\Services;

use App\Http\Requests\Api\Article\SearchRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;

class ArticleService extends Service
{
    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function listing()
    {
        return $this->model->active()
            ->with('user', 'tags', 'categories')
            ->orderBy('updated_at', 'desc')
            ->paginate($this->per_page);
    }

    public function search(SearchRequest $request)
    {
        return $this->model->active()
            ->search($request->word)
            ->with('user', 'tags', 'categories')
            ->orderBy('updated_at', 'desc')
            ->paginate($this->per_page);
    }

    public function byUser(User $user)
    {
        return $user->articles()->active()
            ->with('user', 'tags', 'categories')
            ->orderBy('updated_at', 'desc')
            ->paginate($this->per_page);
    }
    public function byCategory(Category $category)
    {
        return $category->articles()->active()
            ->with('user', 'tags', 'categories')
            ->orderBy('updated_at', 'desc')
            ->paginate($this->per_page);
    }
    public function byTag(Tag $tag)
    {
        return $tag->articles()->active()
            ->with('user', 'tags', 'categories')
            ->orderBy('updated_at', 'desc')
            ->paginate($this->per_page);
    }
}
