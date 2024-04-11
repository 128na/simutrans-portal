<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Models\Article;
use Tests\Feature\TestCase;

final class UserTest extends TestCase
{
    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
    }

    public function test_ユーザーID(): void
    {
        $testResponse = $this->get('api/front/users/'.$this->article->user->id);

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }

    public function test_ニックネーム(): void
    {
        $testResponse = $this->get('api/front/users/'.$this->article->user->nickname);

        $testResponse->assertOk();
    }

    public function test削除ユーザー(): void
    {
        $this->article->user->delete();
        $testResponse = $this->get('api/front/users/'.$this->article->user->id);

        $testResponse->assertNotFound();
    }

    public function test存在しないユーザー(): void
    {
        $testResponse = $this->get('api/front/users/0');

        $testResponse->assertNotFound();
    }
}
