<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\User;

final readonly class AttachmentRepository
{
    public function __construct(private Attachment $attachment) {}

    public function find(null|int|string $id): ?Attachment
    {
        return $this->attachment->find($id);
    }

    public function syncProfile(User $user, int $id): void
    {
        $attachment = $user->myAttachments()->find($id);
        if (! $user->profile) {
            return;
        }

        if (! $attachment) {
            return;
        }

        $user->profile->attachments()->save($attachment);
    }
}
