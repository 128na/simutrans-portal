<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\MyListController;

use App\Models\Article;
use App\Models\MyList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_adds_item_to_list_successfully(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['status' => 'publish']);

        $res = $this->actingAs($user)->postJson("/api/v1/mylist/{$list->id}/items", [
            'article_id' => $article->id,
            'note' => 'Test note',
        ]);

        $res->assertCreated()
            ->assertJsonPath('data.note', 'Test note');

        $this->assertDatabaseHas('mylist_items', [
            'list_id' => $list->id,
            'article_id' => $article->id,
            'note' => 'Test note',
        ]);
    }

    public function test_adds_item_without_note(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['status' => 'publish']);

        $res = $this->actingAs($user)->postJson("/api/v1/mylist/{$list->id}/items", [
            'article_id' => $article->id,
        ]);

        $res->assertCreated()->assertJsonPath('data.note', null);
    }

    public function test_assigns_correct_position_to_new_item(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['status' => 'publish']);

        $res = $this->actingAs($user)->postJson("/api/v1/mylist/{$list->id}/items", [
            'article_id' => $article->id,
        ]);

        $res->assertCreated();
        $position = $res->json('data.position');
        $this->assertIsInt($position);
        $this->assertGreaterThan(0, $position);
    }

    public function test_returns_401_when_unauthenticated(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create();
        $article = Article::factory()->create(['status' => 'publish']);

        $this->postJson("/api/v1/mylist/{$list->id}/items", ['article_id' => $article->id])
            ->assertUnauthorized();
    }

    public function test_returns_403_when_adding_to_other_users_list(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $owner->id]);
        $article = Article::factory()->create(['status' => 'publish']);

        $this->actingAs($otherUser)
            ->postJson("/api/v1/mylist/{$list->id}/items", ['article_id' => $article->id])
            ->assertForbidden();
    }

    public function test_returns_404_when_list_not_found(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['status' => 'publish']);

        $this->actingAs($user)
            ->postJson('/api/v1/mylist/9999/items', ['article_id' => $article->id])
            ->assertNotFound();
    }

    public function test_returns_422_when_article_id_is_missing(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson("/api/v1/mylist/{$list->id}/items", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['article_id']);
    }

    public function test_returns_422_when_article_does_not_exist(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson("/api/v1/mylist/{$list->id}/items", ['article_id' => 9999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['article_id']);
    }

    public function test_returns_422_when_article_already_in_list(): void
    {
        $user = User::factory()->create();
        /** @var MyList $list */
        $list = MyList::factory()->create(['user_id' => $user->id]);
        $article = Article::factory()->create(['status' => 'publish']);

        // 1回目は成功
        $this->actingAs($user)
            ->postJson("/api/v1/mylist/{$list->id}/items", ['article_id' => $article->id])
            ->assertCreated();

        // 2回目は 422 エラー
        $res = $this->actingAs($user)
            ->postJson("/api/v1/mylist/{$list->id}/items", ['article_id' => $article->id]);

        $res->assertUnprocessable()
            ->assertJsonValidationErrors(['article_id']);
        // エラーメッセージの確認
        $this->assertStringContainsString(
            'この記事は既にこのマイリストに追加されています。',
            $res->json('errors.article_id.0')
        );
    }
}
