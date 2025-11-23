<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\TestCase;

/**
 * BaseRepository のテスト
 * Tag モデルを使って BaseRepository の共通メソッドをテストする
 */
final class BaseRepositoryTest extends TestCase
{
    private TestableRepository $repository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new TestableRepository(new Tag);
    }

    public function test_find_正常系(): void
    {
        $tag = Tag::factory()->create(['name' => 'Test Tag']);

        $result = $this->repository->find($tag->id);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($tag->id, $result->id);
        $this->assertEquals('Test Tag', $result->name);
    }

    public function test_find_存在しない_i_dの場合nullを返す(): void
    {
        $result = $this->repository->find(99999);

        $this->assertNull($result);
    }

    public function test_find_or_fail_正常系(): void
    {
        $tag = Tag::factory()->create(['name' => 'Test Tag']);

        $result = $this->repository->findOrFail($tag->id);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals($tag->id, $result->id);
    }

    public function test_find_or_fail_存在しない_i_dの場合例外をスロー(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->findOrFail(99999);
    }

    public function test_store_新規作成(): void
    {
        $data = [
            'name' => 'New Tag',
            'description' => 'Tag Description',
            'editable' => true,
        ];

        $result = $this->repository->store($data);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('New Tag', $result->name);
        $this->assertEquals('Tag Description', $result->description);
        $this->assertTrue($result->editable);
        $this->assertDatabaseHas('tags', ['name' => 'New Tag']);
    }

    public function test_update_更新(): void
    {
        $tag = Tag::factory()->create(['name' => 'Original Name']);

        $this->repository->update($tag, ['name' => 'Updated Name']);

        $tag->refresh();
        $this->assertEquals('Updated Name', $tag->name);
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Updated Name']);
    }

    public function test_delete_削除(): void
    {
        $tag = Tag::factory()->create();
        $tagId = $tag->id;

        $this->repository->delete($tag);

        $this->assertDatabaseMissing('tags', ['id' => $tagId]);
    }

    public function test_find_by_ids_複数の_i_dで検索(): void
    {
        $tag1 = Tag::factory()->create(['name' => 'Tag 1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag 2']);
        $tag3 = Tag::factory()->create(['name' => 'Tag 3']);

        $result = $this->repository->findByIds([$tag1->id, $tag2->id]);

        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($tag1));
        $this->assertTrue($result->contains($tag2));
        $this->assertFalse($result->contains($tag3));
    }

    public function test_find_all_全件取得(): void
    {
        Tag::factory()->count(3)->create();

        $result = $this->repository->findAll();

        $this->assertCount(3, $result);
    }

    public function test_find_all_limit指定(): void
    {
        Tag::factory()->count(5)->create();

        $result = $this->repository->findAll(['*'], [], 2);

        $this->assertCount(2, $result);
    }

    public function test_find_all_特定カラムのみ取得(): void
    {
        Tag::factory()->create(['name' => 'Test Tag', 'description' => 'Description']);

        $result = $this->repository->findAll(['id', 'name']);

        $this->assertCount(1, $result);
        $tag = $result->first();
        $this->assertNotNull($tag->id);
        $this->assertNotNull($tag->name);
        // description はロードされていないことを確認
        $this->assertArrayNotHasKey('description', $tag->getAttributes());
    }

    public function test_paginate_ページネーション(): void
    {
        Tag::factory()->count(30)->create();

        $result = $this->repository->paginate(['*'], [], 10);

        $this->assertCount(10, $result);
        $this->assertEquals(30, $result->total());
    }

    public function test_update_or_create_新規作成(): void
    {
        $data = [
            'name' => 'New Tag',
            'description' => 'Description',
        ];

        $result = $this->repository->updateOrCreate(['name' => 'New Tag'], $data);

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('New Tag', $result->name);
        $this->assertDatabaseHas('tags', ['name' => 'New Tag']);
    }

    public function test_update_or_create_既存レコードの更新(): void
    {
        $tag = Tag::factory()->create(['name' => 'Existing Tag', 'description' => 'Old']);

        $result = $this->repository->updateOrCreate(
            ['name' => 'Existing Tag'],
            ['description' => 'Updated']
        );

        $this->assertEquals($tag->id, $result->id);
        $this->assertEquals('Updated', $result->description);
        $this->assertDatabaseHas('tags', ['name' => 'Existing Tag', 'description' => 'Updated']);
    }

    public function test_first_or_create_既存レコードを取得(): void
    {
        $tag = Tag::factory()->create(['name' => 'Existing Tag']);

        $result = $this->repository->firstOrCreate(['name' => 'Existing Tag']);

        $this->assertEquals($tag->id, $result->id);
        $this->assertCount(1, Tag::where('name', 'Existing Tag')->get());
    }

    public function test_first_or_create_新規作成(): void
    {
        $result = $this->repository->firstOrCreate(
            ['name' => 'New Tag'],
            ['description' => 'Description']
        );

        $this->assertInstanceOf(Tag::class, $result);
        $this->assertEquals('New Tag', $result->name);
        $this->assertDatabaseHas('tags', ['name' => 'New Tag']);
    }

    public function test_load_リレーションのロード(): void
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['created_by' => $user->id]);

        $result = $this->repository->load($tag, ['createdBy']);

        $this->assertTrue($result->relationLoaded('createdBy'));
        $this->assertEquals($user->id, $result->createdBy->id);
    }

    /**
     * storeByUser はリレーション名がモデルの複数形と一致する必要がある
     * Tag の場合、User::Tags() ではなく User::createdTags() なので、
     * このメソッドは Tag モデルでは動作しない。
     * ここでは Article を使用してテストする（User::articles() が存在する）
     */
    public function test_store_by_user_ユーザー経由での作成(): void
    {
        $user = User::factory()->create();
        $articleRepository = new TestableRepository(new Article);

        $data = [
            'post_type' => ArticlePostType::Page,
            'title' => 'Test Article',
            'slug' => 'test-article-'.time(),
            'status' => ArticleStatus::Publish,
            'contents' => 'Test contents',
        ];

        $result = $articleRepository->storeByUser($user, $data);

        $this->assertInstanceOf(Article::class, $result);
        $this->assertEquals('Test Article', $result->title);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertDatabaseHas('articles', ['title' => 'Test Article', 'user_id' => $user->id]);
    }

    public function test_plural_複数形の名前を返す(): void
    {
        $result = $this->repository->publicPlural();

        $this->assertEquals('Tags', $result);
    }

    public function test_singular_単数形の名前を返す(): void
    {
        $result = $this->repository->publicSingular();

        $this->assertEquals('Tag', $result);
    }

    public function test_get_relation_name_リレーション名を返す(): void
    {
        $result = $this->repository->publicGetRelationName();

        $this->assertEquals('Tags', $result);
    }
}

/**
 * テスト用の BaseRepository 実装
 * protected メソッドを public にしてテストできるようにする
 */
final class TestableRepository extends BaseRepository
{
    public function publicPlural(): string
    {
        return $this->plural();
    }

    public function publicSingular(): string
    {
        return $this->singular();
    }

    public function publicGetRelationName(): string
    {
        return $this->getRelationName();
    }
}
