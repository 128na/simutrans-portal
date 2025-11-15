<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\User;

/**
 * @extends BaseRepository<Attachment>
 */
final class AttachmentRepository extends BaseRepository
{
    public function __construct(Attachment $attachment)
    {
        parent::__construct($attachment);
    }

    public function syncProfile(User $user, int $id): void
    {
        $attachment = $user->myAttachments()->find($id);
        if (!$user->profile) {
            return;
        }

        if (!$attachment) {
            return;
        }

        $user->profile->attachments()->save($attachment);
    }
}
