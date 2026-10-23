<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\Article;

use App\Actions\Article\ConvertIntroductionToPost;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\ControllOptionKey;
use App\Events\Article\ArticleUpdated;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Contents\AddonPostContent;
use App\Models\ControllOption;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\Feature\TestCase;

class ConvertToPostTest extends TestCase
{
    private Article $article;

    private Attachment $file;

    private string $url;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->article = $this->createAddonIntroduction();
        $this->file = $this->createAttachment($this->article->user);
        $this->url = '/api/v2/articles/'.$this->article->id.'/convert-to-post';
    }

    public function test_conversion_preserves_contents_relations_identity_and_status(): void
    {
        $thumbnail = $this->createImageAttachment($this->article->user);
        $this->article->attachments()->save($thumbnail);
        $contents = (array) $this->article->contents;
        $contents['thumbnail'] = $thumbnail->id;
        $this->article->update(['contents' => $contents]);
        $category = Category::firstOrFail();
        $tag = Tag::factory()->create();
        $related = Article::factory()->publish()->create();
        $this->article->categories()->attach($category);
        $this->article->tags()->attach($tag);
        $this->article->articles()->attach($related);
        $history = app(ArticleLinkCheckHistoryRepository::class);
        $history->increment($this->article);
        $before = $this->article->fresh();
        Bus::fake();
        Event::fake([ArticleUpdated::class]);
        Notification::fake();

        $this->actingAs($this->article->user)->postJson($this->url, ['file' => $this->file->id])->assertOk()->assertJson(['article_id' => $this->article->id]);
        $after = $this->article->fresh();
        $this->assertSame(ArticlePostType::AddonPost, $after->post_type);
        $this->assertInstanceOf(AddonPostContent::class, $after->contents);
        foreach (['description', 'author', 'license', 'thanks', 'thumbnail'] as $key) {
            $this->assertSame($contents[$key], $after->contents->{$key});
        }
        $raw = json_decode($after->getRawOriginal('contents'), true);
        foreach (['link', 'agreement', 'exclude_link_check'] as $key) {
            $this->assertArrayNotHasKey($key, $raw);
        }
        foreach (['id', 'user_id', 'title', 'slug', 'status', 'published_at'] as $key) {
            $this->assertEquals($before->{$key}, $after->{$key});
        }
        $this->assertTrue($after->modified_at->greaterThan($before->modified_at));
        $this->assertEqualsCanonicalizing([$thumbnail->id, $this->file->id], $after->attachments->modelKeys());
        $this->assertSame([$category->id], $after->categories->modelKeys());
        $this->assertSame([$tag->id], $after->tags->modelKeys());
        $this->assertSame([$related->id], $after->articles->modelKeys());
        $this->assertSame(0, $history->get($after));
        Bus::assertDispatched(JobUpdateRelated::class);
        Event::assertDispatched(ArticleUpdated::class, fn (ArticleUpdated $event): bool => ! $event->shouldNotify && ! $event->notYetPublished);
        Notification::assertNothingSent();
        $this->get(route('articles.download', ['article' => $after]))->assertOk();
    }

    public function test_nonpublic_and_reservation_statuses_are_preserved(): void
    {
        foreach ([ArticleStatus::Private, ArticleStatus::Draft, ArticleStatus::Reservation] as $status) {
            $article = Article::factory()->addonIntroduction()->create(['user_id' => $this->article->user_id, 'status' => $status, 'published_at' => $status === ArticleStatus::Reservation ? now()->addDay() : null]);
            $publishedAt = $article->published_at;
            $file = $this->createAttachment($article->user);
            $this->actingAs($article->user)->postJson('/api/v2/articles/'.$article->id.'/convert-to-post', ['file' => $file->id])->assertOk();
            $this->assertSame($status, $article->fresh()->status);
            $this->assertEquals($publishedAt, $article->fresh()->published_at);
        }
    }

    public function test_authentication_owner_and_update_restrictions_apply_to_both_endpoints(): void
    {
        $this->getJson($this->url)->assertUnauthorized();
        $this->postJson($this->url, ['file' => $this->file->id])->assertUnauthorized();
        $this->actingAs(User::factory()->create())->getJson($this->url)->assertForbidden();
        $this->postJson($this->url, ['file' => $this->file->id])->assertForbidden();
        ControllOption::updateOrCreate(['key' => ControllOptionKey::ArticleUpdate], ['value' => false]);
        $this->actingAs($this->article->user)->getJson($this->url)->assertForbidden();
        $this->postJson($this->url, ['file' => $this->file->id])->assertForbidden();
    }

    public function test_candidate_list_and_submission_reject_unavailable_files(): void
    {
        $other = $this->createAttachment(User::factory()->create());
        $image = $this->createImageAttachment($this->article->user);
        $missing = Attachment::factory()->create(['user_id' => $this->article->user_id, 'path' => 'missing.zip']);
        $assigned = $this->createAttachment($this->article->user);
        Article::factory()->create()->attachments()->save($assigned);
        $this->actingAs($this->article->user)->getJson($this->url)->assertOk()->assertJsonCount(1, 'attachments')->assertJsonPath('attachments.0.id', $this->file->id);
        foreach ([$other->id, $image->id, $missing->id, $assigned->id, 999999] as $id) {
            $this->postJson($this->url, ['file' => $id])->assertUnprocessable()->assertJsonValidationErrors('file');
        }
        $this->postJson($this->url, [])->assertUnprocessable();
        $this->assertSame(ArticlePostType::AddonIntroduction, $this->article->fresh()->post_type);
    }

    public function test_trash_wrong_type_and_double_conversion_are_conflicts(): void
    {
        $this->actingAs($this->article->user);
        $this->article->update(['status' => ArticleStatus::Trash]);
        $this->getJson($this->url)->assertConflict();
        $this->postJson($this->url, ['file' => $this->file->id])->assertConflict();
        $this->article->update(['status' => ArticleStatus::Publish]);
        $this->postJson($this->url, ['file' => $this->file->id])->assertOk();
        $before = $this->article->fresh()->getRawOriginal();
        $this->postJson($this->url, ['file' => $this->file->id])->assertConflict();
        $this->getJson($this->url)->assertConflict();
        $this->assertSame($before, $this->article->fresh()->getRawOriginal());
    }

    public function test_old_introduction_editor_cannot_overwrite_converted_file(): void
    {
        $payload = ['article' => ['id' => $this->article->id, 'post_type' => 'addon-introduction', 'title' => $this->article->title, 'slug' => $this->article->slug, 'status' => 'publish', 'contents' => (array) $this->article->contents, 'articles' => [], 'tags' => [], 'categories' => []]];
        $this->actingAs($this->article->user)->postJson($this->url, ['file' => $this->file->id])->assertOk();
        $before = $this->article->fresh()->getRawOriginal();
        $this->postJson('/api/v2/articles/'.$this->article->id, $payload)->assertConflict();
        $this->assertSame($before, $this->article->fresh()->getRawOriginal());
        $this->assertSame($this->article->id, $this->file->fresh()->attachmentable_id);
    }

    public function test_failure_rolls_back_article_and_attachment_changes(): void
    {
        $before = $this->article->fresh()->getRawOriginal();
        $repository = Mockery::mock(ArticleRepository::class)->makePartial();
        $repository->shouldReceive('syncAttachments')->once()->andThrow(new RuntimeException('failure'));
        $this->app->instance(ArticleRepository::class, $repository);
        try {
            app(ConvertIntroductionToPost::class)($this->article, $this->article->user_id, $this->file->id);
            $this->fail('Expected failure');
        } catch (RuntimeException $exception) {
            $this->assertSame('failure', $exception->getMessage());
        }
        $this->assertSame($before, $this->article->fresh()->getRawOriginal());
        $this->assertNull($this->file->fresh()->attachmentable_id);
    }

    public function test_existing_file_on_same_article_is_available_and_null_common_fields_are_preserved(): void
    {
        $this->article->attachments()->save($this->file);
        $this->article->update(['contents' => ['description' => '本文', 'link' => 'https://example.com']]);
        $this->actingAs($this->article->user)->getJson($this->url)->assertOk()->assertJsonPath('attachments.0.id', $this->file->id);
        $this->postJson($this->url, ['file' => $this->file->id])->assertOk();
        $contents = $this->article->fresh()->contents;
        $this->assertSame('本文', $contents->description);
        $this->assertNull($contents->author);
        $this->assertNull($contents->license);
        $this->assertNull($contents->thanks);
        $this->assertSame($this->file->id, $contents->file);
    }

    public function test_stale_article_object_is_revalidated_and_soft_deleted_article_is_unavailable(): void
    {
        $stale = $this->article;
        Article::findOrFail($stale->id)->update(['status' => ArticleStatus::Trash]);
        try {
            app(ConvertIntroductionToPost::class)($stale, $stale->user_id, $this->file->id);
            $this->fail('Expected conflict');
        } catch (HttpException $exception) {
            $this->assertSame(409, $exception->getStatusCode());
        }
        $this->article->delete();
        $this->actingAs($this->article->user)->getJson($this->url)->assertNotFound();
        $this->postJson($this->url, ['file' => $this->file->id])->assertNotFound();
    }
}
