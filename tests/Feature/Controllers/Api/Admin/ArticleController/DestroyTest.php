<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\ArticleController;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

final class DestroyTest extends TestCase
{
    private User $user;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->admin()->create();
        $this->article = Article::factory()->create();
    }

    public function test論理削除済みでなければ論理削除(): void
    {
        $this->actingAs($this->user);
        $url = '/api/admin/articles/'.$this->article->id;
        $testResponse = $this->deleteJson($url);
        $testResponse->assertOk();

        $article = Article::withTrashed()->findOrFail($this->article->id);
        $this->assertTrue($article->trashed(), '論理削除されている');
    }

    public function test論理削除済みなら復活(): void
    {
        $this->article->delete();

        $this->actingAs($this->user);
        $url = '/api/admin/articles/'.$this->article->id;
        $testResponse = $this->deleteJson($url);
        $testResponse->assertOk();

        $article = Article::withTrashed()->findOrFail($this->article->id);
        $this->assertFalse($article->trashed(), '論理削除されていない');
    }

    public function test未ログイン(): void
    {
        $url = '/api/admin/articles/'.$this->article->id;
        $testResponse = $this->deleteJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test管理者以外(): void
    {
        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $url = '/api/admin/articles/'.$this->article->id;
        $testResponse = $this->deleteJson($url);
        $testResponse->assertUnauthorized();
    }
}
