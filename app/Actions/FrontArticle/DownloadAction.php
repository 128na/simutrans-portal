<?php

declare(strict_types=1);

namespace App\Actions\FrontArticle;

use App\Models\Article;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadAction
{
    public function __invoke(Article $article, ?User $user): StreamedResponse
    {
        if ($user instanceof \App\Models\User && $user->id !== $article->user_id) {
            event(new \App\Events\ArticleConversion($article));
        }

        return $this->getPublicDisk()->download(
            $article->file->path,
            $article->file->original_name
        );
    }

    private function getPublicDisk(): FilesystemAdapter
    {
        return Storage::disk('public');
    }
}
