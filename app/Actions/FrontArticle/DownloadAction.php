<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Events\ArticleConversion;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadAction
{
    /**
     * @param  bool  $countConversion  HEAD リクエストなど本文を返さないときは false
     */
    public function __invoke(Article $article, ?User $user, bool $countConversion = true): StreamedResponse
    {
        /** @var Attachment|null $file */
        $file = $article->file;
        if ($file === null || ! $this->getPublicDisk()->exists($file->path)) {
            abort(404);
        }

        // ログインしていて自身の記事ならカウントしない。ファイルを返せるときだけ 1 リクエスト 1 回数える
        if ($countConversion && (is_null($user) || $user->id !== $article->user_id)) {
            event(new ArticleConversion($article));
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
