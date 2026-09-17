<?php

declare(strict_types=1);

namespace App\Actions\Redirect;

use App\Models\User;
use App\Repositories\RedirectRepository;
use App\Traits\Slugable;
use Illuminate\Support\Facades\Config;

class AddRedirect
{
    public function __construct(
        private RedirectRepository $redirectRepository,
    ) {}

    /**
     * @param  string  $oldSlug  更新前の記事から取得した、urlencode()済みのスラッグ（{@see Slugable}）
     * @param  string  $newSlug  更新後（保存済み）の記事から取得した、urlencode()済みのスラッグ（{@see Slugable}）
     */
    public function __invoke(User $user, string $oldSlug, string $newSlug): void
    {
        $base = Config::string('app.url', '');
        $from = route('articles.show', ['userIdOrNickname' => $user->nickname ?? $user->id, 'articleSlug' => urldecode($oldSlug)]);
        $to = route('articles.show', ['userIdOrNickname' => $user->nickname ?? $user->id, 'articleSlug' => urldecode($newSlug)]);

        $this->redirectRepository->store([
            'user_id' => $user->id,
            'from' => str_replace($base, '', $from),
            'to' => str_replace($base, '', $to),
        ]);
    }
}
