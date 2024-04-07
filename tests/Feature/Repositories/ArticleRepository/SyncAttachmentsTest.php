<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

final class SyncAttachmentsTest extends TestCase
{
    private ArticleRepository $repository;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->repository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $shouldAddAttachment = Attachment::factory()->create([
            'user_id' => $this->user->id,
            'attachmentable_type' => null,
            'attachmentable_id' => null,
        ]);
        $shouldRemoveAttachment = Attachment::factory()->create([
            'user_id' => $this->user->id,
            'attachmentable_type' => Article::class,
            'attachmentable_id' => $article->id,
        ]);

        $this->assertSame(
            [$shouldRemoveAttachment->id],
            $article->attachments()->pluck('id')->toArray()
        );

        $this->repository->syncAttachments($article, [$shouldAddAttachment->id]);

        $this->assertSame(
            [$shouldAddAttachment->id],
            $article->attachments()->pluck('id')->toArray()
        );
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
