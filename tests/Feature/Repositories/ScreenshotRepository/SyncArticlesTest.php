<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ScreenshotRepository;

use App\Models\Article;
use App\Models\Screenshot;
use App\Models\User;
use App\Repositories\ScreenshotRepository;
use Tests\Feature\TestCase;

final class SyncArticlesTest extends TestCase
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
        $oldArticle = Article::factory()->create();
        $screenshot->articles()->save($oldArticle);

        $newArticle = Article::factory()->draft()->create();
        $data = [$newArticle->id];

        $this->screenshotRepository->syncArticles($screenshot, $data);

        $this->assertSame(1, $screenshot->articles()->count());
        $this->assertSame($newArticle->id, $screenshot->articles()->first()->id);
    }

    public function test_削除記事は関連付けない(): void
    {
        $user = User::factory()->create();
        $screenshot = Screenshot::factory()->create(['user_id' => $user->id]);

        $deletedArticle = Article::factory()->create(['deleted_at' => now()]);
        $deletedUser = User::factory()->create(['deleted_at' => now()]);
        $deletedUsersArticle = Article::factory()->create(['user_id' => $deletedUser->id]);

        $data = [
            $deletedArticle->id,
            $deletedUsersArticle->id,
        ];

        $this->screenshotRepository->syncArticles($screenshot, $data);

        $this->assertSame(0, $screenshot->articles()->count());
    }
}
