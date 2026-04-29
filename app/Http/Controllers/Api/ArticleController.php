<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Repositories\Article\MypageArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct(
        private readonly MypageArticleRepository $repository,
    ) {}

    public function index(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        $articles = $this->repository->getForMypageList($user);

        return response()->json([
            'data' => $articles->map(fn (Article $article): array => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'status' => $article->status->value,
                'post_type' => $article->post_type->value,
                'published_at' => $article->published_at?->format('Y/m/d H:i'),
                'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
            ])->values()->all(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        try {
            $article = Article::where('id', $id)
                ->where('user_id', $user->id)
                ->with(['categories', 'tags'])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return response()->json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status->value,
            'post_type' => $article->post_type->value,
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
            'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
            'categories' => $article->categories->map(fn (Category $c): array => [
                'id' => $c->id,
                'slug' => $c->slug,
                'type' => $c->type->value,
            ])->values()->all(),
            'tags' => $article->tags->map(fn (Tag $t): array => [
                'id' => $t->id,
                'name' => $t->name,
            ])->values()->all(),
        ]);
    }
}
