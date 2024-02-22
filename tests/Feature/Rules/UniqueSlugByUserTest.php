<?php

namespace Tests\Feature\Rules;

use App\Models\Article;
use App\Models\User;
use App\Rules\UniqueSlugByUser;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Mockery\MockInterface;
use Tests\TestCase;

class UniqueSlugByUserTest extends TestCase
{
    private User $user2;

    private User $admin1;

    private User $admin2;

    private Closure $failClosure;

    private bool $failCalled = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
        $this->user = User::factory()->create();
        $this->user2 = User::factory()->create();
        $this->admin1 = User::factory()->admin()->create();
        $this->admin2 = User::factory()->admin()->create();
        $this->failCalled = false;

        $mock = $this->mock(PotentiallyTranslatedString::class, fn (MockInterface $mock) => $mock->allows('translate'));
        $this->failClosure = function () use ($mock) {
            $this->failCalled = true;

            return $mock;
        };
    }

    private function getSUT(): UniqueSlugByUser
    {
        return new UniqueSlugByUser;
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

    public function test_一般投稿、他記事と重複_NG(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }

    public function test_一般投稿、他者記事と重複_OK(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user2->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertFalse($this->failCalled);
    }

    public function test_一般投稿、管理者記事と重複_NG(): void
    {
        $article = Article::factory()->create(['user_id' => $this->admin1->id]);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }

    public function test_管理者投稿、編集中記事と重複_OK(): void
    {
        $article = Article::factory()->create(['user_id' => $this->admin1->id]);
        $this->actingAs($this->admin1);
        $this->getSUT()
            ->setData(['article' => ['id' => $article->id]])
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertFalse($this->failCalled);
    }

    public function test_管理者投稿、他記事と重複_NG(): void
    {
        $article = Article::factory()->create(['user_id' => $this->admin1->id]);
        $this->actingAs($this->admin1);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }

    public function test_管理者投稿、他者記事と重複_NG(): void
    {
        $article = Article::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->admin1);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }

    public function test_管理者投稿、管理者記事と重複_NG(): void
    {
        $article = Article::factory()->create(['user_id' => $this->admin2->id]);
        $this->actingAs($this->admin1);
        $this->getSUT()
            ->validate('dummy', $article->slug, $this->failClosure);
        $this->assertTrue($this->failCalled);
    }
}
