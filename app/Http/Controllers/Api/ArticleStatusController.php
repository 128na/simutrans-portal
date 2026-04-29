<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ArticleStatus;
use App\Events\Article\ArticleUpdated;
use App\Http\Controllers\Controller;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ArticleStatusController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $repository,
    ) {}

    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                ArticleStatus::Publish->value,
                ArticleStatus::Draft->value,
                ArticleStatus::Private->value,
                ArticleStatus::Trash->value,
            ])],
        ]);

        try {
            $article = Article::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $newStatus = ArticleStatus::from($validated['status']);
        $notYetPublished = is_null($article->published_at);

        $updateData = [
            'status' => $newStatus,
            'modified_at' => CarbonImmutable::now()->toDateTimeString(),
        ];

        if ($notYetPublished && $newStatus === ArticleStatus::Publish) {
            $updateData['published_at'] = CarbonImmutable::now()->toDateTimeString();
        }

        $this->repository->update($article, $updateData);
        $article->refresh();

        dispatch(new JobUpdateRelated($article->id));
        event(new ArticleUpdated($article, false, false));

        return response()->json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status->value,
            'post_type' => $article->post_type->value,
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
            'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
        ]);
    }
}
