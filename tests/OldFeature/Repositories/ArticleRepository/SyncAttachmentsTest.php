<?php

declare(strict_types=1);

namespace Tests\OldFeature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Tests\TestCase;

class SyncAttachmentsTest extends TestCase
{
    private ArticleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $attachment = Attachment::factory()->create([
            'user_id' => $this->user->id,
            'attachmentable_type' => null,
            'attachmentable_id' => null,
        ]);

        $this->assertDatabaseHas('attachments', [
            'id' => $attachment->id,
            'attachmentable_type' => null,
            'attachmentable_id' => null,
        ]);

        $this->repository->syncAttachments($article, [$attachment->id]);

        $this->assertDatabaseHas('attachments', [
            'id' => $attachment->id,
            'attachmentable_type' => Article::class,
            'attachmentable_id' => $article->id,
        ]);
    }

    public function test他人の添付はNG(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $attachment = Attachment::factory()->create([
            'user_id' => User::factory()->create()->id,
            'attachmentable_type' => null,
            'attachmentable_id' => null,
        ]);

        $this->assertDatabaseHas('attachments', [
            'id' => $attachment->id,
            'attachmentable_type' => null,
            'attachmentable_id' => null,
        ]);

        $this->repository->syncAttachments($article, [$attachment->id]);

        $this->assertDatabaseHas('attachments', [
            'id' => $attachment->id,
            'attachmentable_type' => null,
            'attachmentable_id' => null,
        ]);
    }
}
