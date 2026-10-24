<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Actions\Article\ConvertIntroductionToPost;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\ConvertToPostRequest;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Models\Article;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ConvertToPostController extends Controller
{
    public function show(Article $article, ConvertIntroductionToPost $convert): JsonResponse
    {
        $convert->ensureConvertible($article, $this->loggedinUser()->id);
        $attachments = $this->loggedinUser()->myAttachments()->with('fileInfo')
            ->where(function (Builder $query) use ($article): void {
                $query->where(fn (Builder $query) => $query->whereNull('attachmentable_id')->whereNull('attachmentable_type'))
                    ->orWhere(fn (Builder $query) => $query->where('attachmentable_id', $article->id)->where('attachmentable_type', $article->getMorphClass()));
            })->get()->filter(fn (Attachment $attachment): bool => $convert->isAvailable($attachment, $article));

        return response()->json(['attachments' => AttachmentEdit::collection($attachments->values())]);
    }

    public function store(ConvertToPostRequest $request, Article $article, ConvertIntroductionToPost $convert): JsonResponse
    {
        $article = $convert($article, $this->loggedinUser()->id, (int) $request->validated('file'));

        return response()->json(['article_id' => $article->id]);
    }
}
