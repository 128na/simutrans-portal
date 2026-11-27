<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Events\ArticleShown;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Redirect;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCase;

class ShowControllerTest extends TestCase
{
    public function test_show_元画像のurlは画像の添付だけに出す(): void
    {
        $disk = Storage::fake('public');
        $imagePath = (string) UploadedFile::fake()->image('photo.png')->store('user/1', 'public');
        $disk->put('user/1/addon.txt', 'addon');
        $article = Article::factory()->publish()->create();
        $image = Attachment::factory()->for($article, 'attachmentable')->create([
            'user_id' => $article->user_id,
            'path' => $imagePath,
            'thumbnail_path' => 'thumbnails/photo_thumb.webp',
        ]);
        $file = Attachment::factory()->for($article, 'attachmentable')->create([
            'user_id' => $article->user_id,
            'path' => 'user/1/addon.txt',
        ]);

        $testResponse = $this->getJson(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
        $attachments = collect($testResponse->json('attachments'))->keyBy('id');
        $this->assertSame($disk->url($imagePath), $attachments[$image->id]['url']);
        $this->assertSame($disk->url('thumbnails/photo_thumb.webp'), $attachments[$image->id]['thumbnail']);
        // アドオンのファイルは直接の URL を出さない（ダウンロードは計測と回数制限を通す）
        $this->assertArrayNotHasKey('url', $attachments[$file->id]);
    }

    public function test_show_持ち主の閲覧は閲覧数に数えない(): void
    {
        Event::fake([ArticleShown::class]);
        $article = Article::factory()->publish()->create();
        $owner = $article->user;
        $this->assertInstanceOf(User::class, $owner);

        $this->actingAs($owner)->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]))->assertOk();

        Event::assertNotDispatched(ArticleShown::class);
    }

    public function test_show_他人と未ログインの閲覧は閲覧数に数える(): void
    {
        Event::fake([ArticleShown::class]);
        $other = User::factory()->create();
        $article = Article::factory()->publish()->create();
        $this->assertNotSame($other->id, $article->user_id);
        $url = route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]);

        $this->get($url)->assertOk();
        $this->actingAs($other)->get($url)->assertOk();

        Event::assertDispatchedTimes(ArticleShown::class, 2);
    }

    public function test_show(): void
    {
        $article = Article::factory()->publish()->create();
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]));
        $testResponse->assertOk();

        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user->nickname,
            'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
    }

    public function test_show非公開(): void
    {
        $article = Article::factory()->create(['status' => 'private']);
        $testResponse = $this->get(route('articles.show', ['userIdOrNickname' => $article->user->nickname ?? $article->user_id, 'articleSlug' => $article->slug]));

        $testResponse->assertNotFound();
    }

    public function test_show_リダイレクトあり(): void
    {
        $article = Article::factory()->publish()->create();

        $from = '/users/'.$article->user_id.'/dummy';
        $to = '/users/'.$article->user_id.$article->slug;
        Redirect::create(['user_id' => $article->user_id, 'from' => $from, 'to' => $to]);
        $testResponse = $this->get(route('articles.show', ['userIdOrNickname' => $article->user_id, 'articleSlug' => 'dummy']));

        $testResponse->assertRedirect($to);
    }

    public function test_fallback_show_ニックネーム設定済み(): void
    {
        $article = Article::factory()->publish()->create();

        $testResponse = $this->get('/articles/'.$article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));

        $testResponse = $this->get('/articles/'.$article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user->nickname, $article->slug));
    }

    public function test_fallback_show_ニックネーム未設定(): void
    {
        $article = Article::factory()->publish()->create();
        $article->user->update(['nickname' => null]);

        $testResponse = $this->get('/articles/'.$article->slug);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));

        $testResponse = $this->get('/articles/'.$article->id);
        $testResponse->assertRedirect(sprintf('/users/%s/%s', $article->user_id, $article->slug));
    }

    public function test_show_json_response(): void
    {
        $article = Article::factory()->publish()->create();

        // Test with .json extension
        $testResponse = $this->get(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug.'.json',
        ]));

        $testResponse->assertOk();
        $testResponse->assertJson([
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
        ]);

        // Test with Accept: application/json header
        $testResponse = $this->getJson(route('articles.show', [
            'userIdOrNickname' => $article->user_id,
            'articleSlug' => $article->slug,
        ]));

        $testResponse->assertOk();
        $testResponse->assertJson([
            'id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
        ]);
    }
}
