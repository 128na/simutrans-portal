<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Events\ArticleConversion;
use App\Models\Article;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadAction
{
    public function __invoke(Article $article, ?User $user): StreamedResponse
    {
        // ログインしていて自身の記事ならカウントしない
        if (is_null($user) || $user->id !== $article->user_id) {
            event(new ArticleConversion($article));
        }

        $file = $article->file;
        if ($file === null) {
            abort(404);
        }

        return $this->getPublicDisk()->download(
            $file->path,
            $file->original_name
        );
    }

    private function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }
}
