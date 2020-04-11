<?php

namespace Tests\Feature\Api\v2\Mypage;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VerifiedTest extends TestCase
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
        $attachment = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $user->id);
        $this->actingAs($user);

        // need not verify
        $response = $this->getJson(route('api.v2.users.index'));
        $response->assertStatus(200);
        $response = $this->getJson(route('api.v2.tags.search'));
        $response->assertStatus(200);
        $response = $this->getJson(route('api.v2.attachments.index'));
        $response->assertStatus(200);
        $response = $this->getJson(route('api.v2.articles.index'));
        $response->assertStatus(200);
        $response = $this->getJson(route('api.v2.articles.options'));
        $response->assertStatus(200);

        // need verify
        $response = $this->postJson(route('api.v2.users.update'));
        $response->assertForbidden();
        $response = $this->postJson(route('api.v2.tags.store'));
        $response->assertForbidden();
        $response = $this->postJson(route('api.v2.attachments.store'));
        $response->assertForbidden();
        $response = $this->deleteJson(route('api.v2.attachments.destroy', $attachment));
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
        $attachment = Attachment::createFromFile(UploadedFile::fake()->image('thumbnail.jpg', 1), $user->id);
        $this->actingAs($user);

        // need not verify
        $response = $this->getJson(route('api.v2.users.index'));
        $response->assertStatus(200);
        $response = $this->getJson(route('api.v2.tags.search'));
        $response->assertStatus(200);
        $response = $this->getJson(route('api.v2.attachments.index'));
        $response->assertStatus(200);
        $response = $this->getJson(route('api.v2.articles.index'));
        $response->assertStatus(200);
        $response = $this->getJson(route('api.v2.articles.options'));
        $response->assertStatus(200);

        // need verify
        $response = $this->postJson(route('api.v2.users.update'));
        $response->assertStatus(422);
        $response = $this->postJson(route('api.v2.tags.store'));
        $response->assertStatus(422);
        $response = $this->postJson(route('api.v2.attachments.store'));
        $response->assertStatus(422);
        $response = $this->deleteJson(route('api.v2.attachments.destroy', $attachment));
        $response->assertStatus(200);
        $response = $this->postJson(route('api.v2.articles.store'));
        $response->assertStatus(422);
        $response = $this->postJson(route('api.v2.articles.update', $article));
        $response->assertStatus(422);
    }

}
