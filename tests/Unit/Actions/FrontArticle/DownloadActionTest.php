<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\FrontArticle;

use App\Actions\FrontArticle\DownloadAction;
use App\Events\ArticleConversion;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Unit\TestCase;

final class DownloadActionTest extends TestCase
{
    public function test_dispatches_conversion_event_for_guest(): void
    {
        Event::fake();
        Storage::fake('public');

        $attachment = Attachment::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'path' => 'test/file.zip',
            'original_name' => 'test.zip',
        ]);

        $article = Article::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'post_type' => 'addon-post',
            'contents' => ['file' => 1, 'author' => 'Test Author', 'description' => 'Test'],
        ]);
        $article->setRelation('attachments', collect([$attachment]));

        Storage::disk('public')->put('test/file.zip', 'dummy content');

        $action = new DownloadAction;
        $response = $action($article, null);

        Event::assertDispatched(ArticleConversion::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
    }

    public function test_dispatches_conversion_event_for_other_user(): void
    {
        Event::fake();
        Storage::fake('public');

        $author = User::factory()->make(['id' => 1]);
        $otherUser = User::factory()->make(['id' => 2]);

        $attachment = Attachment::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'path' => 'test/file.zip',
            'original_name' => 'test.zip',
        ]);

        $article = Article::factory()->make([
            'id' => 1,
            'user_id' => $author->id,
            'post_type' => 'addon-post',
            'contents' => ['file' => 1, 'author' => 'Test Author', 'description' => 'Test'],
        ]);
        $article->setRelation('attachments', collect([$attachment]));

        Storage::disk('public')->put('test/file.zip', 'dummy content');

        $action = new DownloadAction;
        $response = $action($article, $otherUser);

        Event::assertDispatched(ArticleConversion::class);
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
    }

    public function test_does_not_dispatch_event_for_article_author(): void
    {
        Event::fake();
        Storage::fake('public');

        $author = User::factory()->make(['id' => 1]);

        $attachment = Attachment::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'path' => 'test/file.zip',
            'original_name' => 'test.zip',
        ]);

        $article = Article::factory()->make([
            'id' => 1,
            'user_id' => $author->id,
            'post_type' => 'addon-post',
            'contents' => ['file' => 1, 'author' => 'Test Author', 'description' => 'Test'],
        ]);
        $article->setRelation('attachments', collect([$attachment]));

        Storage::disk('public')->put('test/file.zip', 'dummy content');

        $action = new DownloadAction;
        $response = $action($article, $author);

        Event::assertNotDispatched(ArticleConversion::class);
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
    }

    public function test_aborts_when_no_file_attached(): void
    {
        Event::fake();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $article = Article::factory()->make([
            'id' => 1,
            'user_id' => 1,
            'post_type' => 'addon-post',
            'contents' => ['file' => null, 'author' => 'Test Author', 'description' => 'Test'],
        ]);
        $article->setRelation('attachments', collect());

        $action = new DownloadAction;
        $action($article, null);
    }
}
