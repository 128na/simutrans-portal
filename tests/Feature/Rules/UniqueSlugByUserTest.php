<?php

namespace Tests\Feature\Rules;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use App\Rules\UniqueSlugByUser;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class UniqueSlugByUserTest extends TestCase
{
    private User $user;

    private Closure $failClosure;

    private bool $failCalled = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->failCalled = false;

        $mock = $this->mock(PotentiallyTranslatedString::class, fn (MockInterface $mock) => $mock->allows('translate'));
        $this->failClosure = function () use ($mock) {
            $this->failCalled = true;

            return $mock;
        };
    }

    private function getSUT(): UniqueSlugByUser
    {
        return new UniqueSlugByUser();
    }

    public function test_一般投稿、編集中記事と重複_OK(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->setData(['article' => ['id' => $article->id]])
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertFalse($this->failCalled);
    }

    public function test_一般投稿、自身の既存記事と重複_NG(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }

    public function test_一般投稿、他者の記事と重複_OK(): void
    {
        $article = Article::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertFalse($this->failCalled);
    }

    public function test_一般投稿、管理者の記事と重複_NG(): void
    {
        $article = Article::factory()->create(['user_id' => User::factory()->admin()->create()->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }

    public function test_管理者投稿、編集中の記事と重複_OK(): void
    {
        $this->user->update(['role' => UserRole::Admin]);
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->setData(['article' => ['id' => $article->id]])
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertFalse($this->failCalled);
    }

    public function test_管理者投稿、自身の他記事と重複_NG(): void
    {
        $this->user->update(['role' => UserRole::Admin]);
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }

    public function test_管理者投稿、他者の記事と重複_NG(): void
    {
        $this->user->update(['role' => UserRole::Admin]);
        $article = Article::factory()->create();
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }

    public function test_管理者投稿、管理者の記事と重複_NG(): void
    {
        $this->user->update(['role' => UserRole::Admin]);
        $article = Article::factory()->create(['user_id' => User::factory()->admin()->create()->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }
}
