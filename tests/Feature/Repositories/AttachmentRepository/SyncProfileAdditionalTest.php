<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\AttachmentRepository;

use App\Models\Attachment;
use App\Models\User;
use App\Repositories\AttachmentRepository;
use Tests\Feature\TestCase;

final class SyncProfileAdditionalTest extends TestCase
{
    private AttachmentRepository $attachmentRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->attachmentRepository = app(AttachmentRepository::class);
    }

    public function test_sync_profile_with_no_profile(): void
    {
        $user = User::factory()->create();
        $attachment = Attachment::factory()->for($user)->create();

        // user without profile should handle gracefully
        $this->attachmentRepository->syncProfile($user, $attachment->id);

        // no error should occur
        $this->assertTrue(true);
    }

    public function test_sync_profile_attachment_not_found(): void
    {
        $user = User::factory()->create();

        // non-existent attachment id should handle gracefully
        $this->attachmentRepository->syncProfile($user, 99999);

        // no error should occur
        $this->assertTrue(true);
    }
}
