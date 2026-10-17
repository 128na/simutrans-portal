<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Actions\Article\DeleteArticle;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class DestroyController extends Controller
{
    /**
     * 記事を削除
     */
    public function destroy(Article $article, DeleteArticle $deleteArticle): JsonResponse
    {
        $this->authorize('delete', $article);

        $deleteArticle($article);

        return response()->json(['article_id' => $article->id], 200);
    }
}
