<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ScreenshotRepository;

use App\Models\Attachment;
use App\Models\Screenshot;
use App\Models\User;
use App\Repositories\ScreenshotRepository;
use Tests\Feature\TestCase;

final class SyncAttachmentsWithTest extends TestCase
{
    private ScreenshotRepository $screenshotRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->screenshotRepository = app(ScreenshotRepository::class);
    }

    public function test(): void
    {
        $user = User::factory()->create();
        $screenshot = Screenshot::factory()->create(['user_id' => $user->id]);
        $oldAttachment = Attachment::factory()->create(['user_id' => $user->id]);
        $screenshot->attachments()->save($oldAttachment);

        $newAttachment = Attachment::factory()->create(['user_id' => $user->id]);
        $data = [[
            'id' => $newAttachment->id,
            'caption' => 'dummy caption',
            'order' => 123,
        ]];

        $this->screenshotRepository->syncAttachmentsWith($screenshot, $data);

        $newAttachment->refresh();
        $this->assertSame('dummy caption', $newAttachment->caption);
        $this->assertSame(123, $newAttachment->order);
        $this->assertSame(1, $screenshot->attachments()->count());
    }

    public function test_他人のデータは更新しない(): void
    {
        $user = User::factory()->create();
        $screenshot = Screenshot::factory()->create(['user_id' => $user->id]);

        $attachment = Attachment::factory()->create();
        $data = [[
            'id' => $attachment->id,
            'caption' => 'dummy caption',
            'order' => 123,
        ]];

        $this->screenshotRepository->syncAttachmentsWith($screenshot, $data);

        $attachment->refresh();
        $this->assertSame(null, $attachment->caption);
        $this->assertSame(0, $attachment->order);
        $this->assertSame(0, $screenshot->attachments()->count());
    }
}
