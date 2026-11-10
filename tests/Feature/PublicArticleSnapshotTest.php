<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Article\DeleteArticle;
use App\Actions\FrontArticle\LatestAction;
use App\Actions\User\DeleteAccount;
use App\Enums\CategoryType;
use App\Events\Article\ArticleDeleted;
use App\Events\User\UserWithdrawn;
use App\Models\Category;
use App\Services\Front\PublicArticleSnapshot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use PDOException;

class PublicArticleSnapshotTest extends TestCase
{
    public function test_failed_generation_preserves_last_success_until_it_expires(): void
    {
        $announcement = $this->createAnnounce();
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $this->mock(LatestAction::class)
            ->shouldReceive('cardsByPak')->once()->andThrow(new PDOException('Connection refused'));
        $this->artisan('article:refresh-public-snapshot')->assertFailed();

        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $this->get(route('index'))->assertOk()->assertSee($announcement->title);
            $this->assertSame([], DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }
        $this->travel(24)->hours();
        $this->get(route('index'))->assertOk()->assertDontSee($announcement->title);
    }

    public function test_missing_and_corrupt_snapshots_leave_links_available(): void
    {
        $this->get(route('index'))->assertOk()->assertSee(route('latest'))->assertSee(route('announces'));

        $path = config()->string('public-article-snapshot.path');
        File::ensureDirectoryExists(dirname($path));
        File::put($path, '{"version":1,"latest":');

        $this->get(route('index'))->assertOk()->assertSee(route('latest'))->assertSee(route('announces'));
        $this->get(route('latest'))->assertOk();
    }

    public function test_concurrent_refresh_cannot_restore_an_article_deleted_during_generation(): void
    {
        Event::fake([ArticleDeleted::class]);
        $article = $this->createAddonIntroduction();
        $article->categories()->attach(Category::where('type', CategoryType::Pak)->where('slug', '64')->sole());
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $snapshot = app(PublicArticleSnapshot::class);
        $stale = $snapshot->read();
        $this->assertSame([$article->id], array_column($stale['latest']['64'], 'id'));

        $published = $snapshot->refresh(function () use ($snapshot, $stale, $article): array {
            $this->assertFalse($snapshot->refresh(fn () => $stale));
            app(DeleteArticle::class)($article);

            return $stale;
        });

        $this->assertFalse($published);
        $this->get(route('latest'))->assertOk()->assertDontSee($article->title);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $this->get(route('latest'))->assertOk()->assertDontSee($article->title);
    }

    public function test_a_failed_invalidation_leaves_the_article_in_the_database(): void
    {
        $article = $this->createAddonIntroduction();
        File::ensureDirectoryExists(config()->string('public-article-snapshot.path'));

        $this->actingAs($article->user)->deleteJson('/api/v2/articles/'.$article->id)->assertServerError();

        $this->assertNotSoftDeleted($article);
    }

    public function test_withdrawal_removes_the_users_announcements_before_the_next_refresh(): void
    {
        Event::fake([UserWithdrawn::class]);
        $announcement = $this->createAnnounce();
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $this->get(route('index'))->assertOk()->assertSee($announcement->title);

        app(DeleteAccount::class)($announcement->user);

        $this->get(route('index'))->assertOk()->assertDontSee($announcement->title);
    }
}
