<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\User;

class AttachmentRepository
{
    public function __construct(private Attachment $attachment) {}

    public function find(null|int|string $id): ?Attachment
    {
        return $this->attachment->find($id);
    }

    /**
     * 添付ファイルを更新
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Attachment $attachment, array $data): bool
    {
        return $attachment->update($data);
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
