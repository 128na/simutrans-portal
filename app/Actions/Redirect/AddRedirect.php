<?php

declare(strict_types=1);

namespace App\Actions\Redirect;

use App\Models\User;
use App\Repositories\RedirectRepository;
use Illuminate\Support\Facades\Config;

final readonly class AddRedirect
{
    public function __construct(
        private RedirectRepository $redirectRepository,
    ) {}

    public function __invoke(User $user, string $oldSlug, string $newSlug): void
    {
        $base = Config::string('app.url', '');
        $from = route('articles.show', ['userIdOrNickname' => $user->nickname ?? $user->id, 'articleSlug' => $oldSlug]);
        $to = route('articles.show', ['userIdOrNickname' => $user->nickname ?? $user->id, 'articleSlug' => $newSlug]);

        $this->redirectRepository->store([
            'user_id' => $user->id,
            'from' => str_replace($base, '', $from),
            'to' => str_replace($base, '', $to),
        ]);
    }
}
