<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Requests\Api\Admin\ArticleUpdateRequest;

class ArticleController extends Controller
{
    const ARTICLE_COLUMNS = [
        'id',
        'user_id',
        'title',
        'slug',
        'post_type',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    const USER_COLUMNS = [
        'id',
        'name',
    ];

    public function index()
    {
        return Article::select(self::ARTICLE_COLUMNS)
            ->withTrashed()
            ->withUserTrashed()
            ->with(['user' => fn ($q) => $q->select(self::USER_COLUMNS)->withTrashed()])
            ->get();
    }
    public function update(ArticleUpdateRequest $request, int $id)
    {
        Article::withTrashed()
            ->withUserTrashed()
            ->findOrFail($id)
            ->update($request->validated()['article'] ?? []);
        return $this->index();
    }
    public function destroy(int $id)
    {
        tap(Article::withTrashed()
            ->withUserTrashed()
            ->findOrFail($id), function ($a) {
                $a->deleted_at
                    ? $a->restore()
                    : $a->delete();
            });
        return $this->index();
    }
}
