<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\ArticleController;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

final class UpdateTest extends TestCase
{
    private User $user;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setup();

        $this->user = User::factory()->admin()->create();
        $this->article = Article::factory()->publish()->create();
    }

    public function test(): void
    {
        $this->actingAs($this->user);
        $url = '/api/admin/articles/'.$this->article->id;
        $testResponse = $this->putJson($url, ['article' => ['status' => 'draft']]);
        $testResponse->assertOk();

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'status' => 'draft',
        ]);
    }

    public function test未ログイン(): void
    {
        $url = '/api/admin/articles/'.$this->article->id;
        $testResponse = $this->putJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test管理者以外(): void
    {
        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $url = '/api/admin/articles/'.$this->article->id;
        $testResponse = $this->putJson($url);
        $testResponse->assertUnauthorized();
    }
}
