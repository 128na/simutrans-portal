<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\AttachmentRepository;

use App\Models\Attachment;
use App\Models\User;
use App\Repositories\AttachmentRepository;
use Tests\Feature\TestCase;

class SyncProfileTest extends TestCase
{
    private AttachmentRepository $attachmentRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->attachmentRepository = app(AttachmentRepository::class);
    }

    public function test(): void
    {
        $user = User::factory()->create();
        $attachment = Attachment::factory()->create(['user_id' => $user]);
        $this->assertSame(0, $user->profile->attachments()->count());

        $this->attachmentRepository->syncProfile($user, $attachment->id);

        $this->assertSame(1, $user->profile->attachments()->count());
    }

    public function test_他人のファイルは関連付けない(): void
    {
        $user = User::factory()->create();
        $attachment = Attachment::factory()->create();
        $this->assertSame(0, $user->profile->attachments()->count());

        $this->attachmentRepository->syncProfile($user, $attachment->id);

        $this->assertSame(0, $user->profile->attachments()->count());
    }
}
