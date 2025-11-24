<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\User;

final class AttachmentRepository
{
    public function __construct(private readonly Attachment $model) {}

    public function find(int|string|null $id): ?Attachment
    {
        return $this->model->find($id);
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
