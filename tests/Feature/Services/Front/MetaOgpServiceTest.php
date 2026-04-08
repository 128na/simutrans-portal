<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Front;

use App\Models\Article;
use App\Models\User;
use App\Services\Front\MetaOgpService;
use Tests\Feature\TestCase;

class MetaOgpServiceTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_show_returns_expected_structure(): void
    {
        config(['app.name' => 'SimuPortal', 'app.url' => 'http://localhost', 'app.meta-description' => 'default']);

        $user = User::factory()->create(['nickname' => uniqid('carol_')]);
        $article = Article::factory()->create(['user_id' => $user->id, 'title' => 'Test Title', 'slug' => 'test-slug']);

        // set thumbnail attributes on the model instance (no DB column required)
        $sut = new MetaOgpService;

        // Default factory-created article usually has no thumbnail -> image should be null
        $meta = $sut->frontArticleShow($user, $article);
        $this->assertArrayHasKey('title', $meta);
        $this->assertArrayHasKey('description', $meta);
        $this->assertArrayHasKey('image', $meta);
        $this->assertArrayHasKey('canonical', $meta);
        $this->assertArrayHasKey('card_type', $meta);

        $this->assertStringContainsString($article->title, $meta['title']);
        $this->assertStringContainsString(config('app.name'), $meta['title']);
        $this->assertNull($meta['image']);
        $this->assertStringContainsString($article->slug, $meta['canonical']);

        // Create an attachment and set the article's contents.thumbnail to the attachment id
        $attachment = \App\Models\Attachment::factory()->create([
            'attachmentable_type' => \App\Models\Article::class,
            'attachmentable_id' => $article->id,
            'user_id' => $user->id,
            'path' => 'default/test.png',
        ]);

        $raw = $article->getRawOriginal('contents');
        $contentsArr = is_string($raw) ? json_decode($raw, true) : (array) $raw;
        $contentsArr['thumbnail'] = $attachment->id;
        $article->update(['contents' => $contentsArr]);

        $meta2 = $sut->frontArticleShow($user, $article);
        $this->assertIsString($meta2['image']);
        $this->assertStringContainsString($attachment->path, $meta2['image']);
    }

    public function test_pak_and_announces_return_titles_with_app_name(): void
    {
        config(['app.name' => 'SimuPortal']);

        $sut = new MetaOgpService;

        $pak = $sut->frontPak('pak128');
        $this->assertArrayHasKey('title', $pak);
        $this->assertStringContainsString(config('app.name'), $pak['title']);

        $ann = $sut->frontAnnounces();
        $this->assertArrayHasKey('title', $ann);
        $this->assertStringContainsString(config('app.name'), $ann['title']);
    }
}
