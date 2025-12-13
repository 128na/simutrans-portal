<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\Attachment;

use App\Models\Attachment;
use App\Repositories\Attachment\FileInfoRepository;
use Tests\Feature\TestCase;

class FileInfoRepositoryTest extends TestCase
{
    private FileInfoRepository $fileInfoRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->fileInfoRepository = app(FileInfoRepository::class);
    }

    public function test_store_find_update_delete(): void
    {
        $attachment = Attachment::factory()->create();

        $data = [
            'attachment_id' => $attachment->id,
            'data' => ['width' => 800, 'height' => 600],
        ];

        $fileInfo = $this->fileInfoRepository->store($data);

        $this->assertSame($attachment->id, $fileInfo->attachment_id);

        $found = $this->fileInfoRepository->find($fileInfo->id);
        $this->assertNotNull($found);
        $this->assertSame($fileInfo->id, $found->id);

        $this->fileInfoRepository->update($fileInfo, ['data' => ['width' => 1024, 'height' => 768]]);
        $fileInfo->refresh();
        $this->assertSame(1024, $fileInfo->data['width']);

        $this->fileInfoRepository->delete($fileInfo);
        $this->assertNull($this->fileInfoRepository->find($fileInfo->id));
    }
}
