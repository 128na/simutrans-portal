<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\AttachmentRepository;

use App\Models\Attachment;
use App\Models\User;
use App\Repositories\AttachmentRepository;
use Tests\Feature\TestCase;

final class FindAllByUserTest extends TestCase
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

        $result = $this->attachmentRepository->findAllByUser($user);

        $this->assertSame($attachment->id, $result[0]->id);
    }

    public function test_他人のファイルは取得できない(): void
    {
        $user = User::factory()->create();
        Attachment::factory()->create();
        $this->assertSame(0, $user->profile->attachments()->count());

        $result = $this->attachmentRepository->findAllByUser($user);

        $this->assertSame(0, $result->count());
    }
}
