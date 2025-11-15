<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\TagRepository;

use App\Models\User;
use App\Repositories\TagRepository;
use Tests\Feature\TestCase;

final class StoreUpdateTest extends TestCase
{
    private TagRepository $tagRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRepository = app(TagRepository::class);
    }

    public function test_store_and_update(): void
    {
        $actor = User::factory()->create();

        $data = [
            'name' => 'new-tag',
            'description' => 'desc',
            'last_modified_at' => now(),
            'last_modified_by' => $actor->id,
        ];

        $tag = $this->tagRepository->store($data);

        $this->assertSame('new-tag', $tag->name);

        $updated = $this->tagRepository->update($tag, ['description' => 'updated']);

        $this->assertSame('updated', $updated->description);
    }
}
