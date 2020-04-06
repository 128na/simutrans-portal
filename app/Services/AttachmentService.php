<?php
namespace App\Services;

use App\Http\Requests\Api\Attachment\StoreRequest;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AttachmentService extends Service
{
    public function __construct(Attachment $model)
    {
        $this->model = $model;
    }

    public function getAttachments(User $user)
    {
        return $user->myAttachments;
    }

    public function getCreateArchiveAttachments(User $user)
    {
        return $user->myAttachments()
            ->whereNull('attachmentable_id')
            ->whereNull('attachmentable_type')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($attachment) {
                return $attachment->path_exists;
            });
    }

    public function getUpdateArchiveAttachments(User $user, Article $article)
    {
        return $user->myAttachments()
            ->where(function ($query) {
                $query
                    ->whereNull('attachmentable_id')
                    ->whereNull('attachmentable_type');
            })
            ->orWhere(function ($query) use ($article) {
                $query
                    ->where('attachmentable_id', $article->id)
                    ->where('attachmentable_type', Article::class);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function ($attachment) {
                return $attachment->path_exists;
            });
    }

    public function createFromFile(User $user, StoreRequest $request)
    {
        return $this->model->createFromFile($request->file, $user->id);
    }

    public function destroy(Attachment $attachment)
    {
        return $attachment->delete();
    }
}
