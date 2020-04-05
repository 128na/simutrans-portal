<?php

namespace Tests\Feature\Api\v2\ArticleEditor;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VeryfiedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testNotVerified()
    {
        $user = factory(User::class)->create(['email_verified_at' => null]);
        $article = factory(Article::class)->create(['user_id' => $user->id]);
        $attachment = Attachment::createFromFile(UploadedFile::fake()->create('thumbnail.jpg', 1), $user->id);
        $this->actingAs($user);

        // tags
        $response = $this->getJson(route('api.v2.tags.search'));
        $response->assertForbidden();
        $response = $this->postJson(route('api.v2.tags.store'));
        $response->assertForbidden();

        // attachments
        $response = $this->getJson(route('api.v2.attachments.index'));
        $response->assertForbidden();

        $response = $this->postJson(route('api.v2.attachments.store'));
        $response->assertForbidden();

        $response = $this->deleteJson(route('api.v2.attachments.destroy', $attachment));
        $response->assertForbidden();

        // articles
        $response = $this->getJson(route('api.v2.articles.options'));
        $response->assertForbidden();

        $response = $this->postJson(route('api.v2.articles.store'));
        $response->assertForbidden();

        $response = $this->postJson(route('api.v2.articles.update', $article));
        $response->assertForbidden();
    }

    public function testVerified()
    {
        $user = factory(User::class)->create();
        $article = factory(Article::class)->create(['user_id' => $user->id]);
        $attachment = Attachment::createFromFile(UploadedFile::fake()->create('thumbnail.jpg', 1), $user->id);
        $this->actingAs($user);

        // tags
        $response = $this->getJson(route('api.v2.tags.search'));
        $response->assertStatus(200);
        $response = $this->postJson(route('api.v2.tags.store'));
        $response->assertStatus(422);

        // attachments
        $response = $this->getJson(route('api.v2.attachments.index'));
        $response->assertStatus(200);

        $response = $this->postJson(route('api.v2.attachments.store'));
        $response->assertStatus(422);

        $response = $this->deleteJson(route('api.v2.attachments.destroy', $attachment));
        $response->assertStatus(200);

        // articles
        $response = $this->getJson(route('api.v2.articles.options'));
        $response->assertStatus(200);

        $response = $this->postJson(route('api.v2.articles.store'));
        $response->assertStatus(422);

        $response = $this->postJson(route('api.v2.articles.update', $article));
        $response->assertStatus(422);
    }

}
