<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use App\Repositories\MyListItemRepository;
use App\Repositories\MyListRepository;
use App\Services\MyListService;
use Mockery;
use Tests\Unit\TestCase;

class MyListServiceTest extends TestCase
{
    private MyListRepository $listRepository;

    private MyListItemRepository $itemRepository;

    private MyListService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listRepository = Mockery::mock(MyListRepository::class);
        $this->itemRepository = Mockery::mock(MyListItemRepository::class);
        $this->service = new MyListService($this->listRepository, $this->itemRepository);
    }

    public function test_create_list_generates_slug_when_public(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;

        $list = Mockery::mock(MyList::class)->makePartial();
        $list->id = 123;

        $this->listRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($user) {
                return $data['user_id'] === $user->id
                    && $data['title'] === 'Test'
                    && $data['is_public'] === true
                    && isset($data['slug']);
            }))
            ->andReturn($list);

        $this->listRepository->shouldReceive('update')
            ->once()
            ->with($list, []);

        $result = $this->service->createList($user, 'Test', null, true);

        $this->assertSame($list, $result);
    }

    public function test_create_list_generates_slug_when_private(): void
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;

        $list = Mockery::mock(MyList::class)->makePartial();
        $list->id = 123;

        $this->listRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($user) {
                return $data['user_id'] === $user->id
                    && $data['title'] === 'Test'
                    && $data['is_public'] === false
                    && isset($data['slug']);
            }))
            ->andReturn($list);

        $this->listRepository->shouldReceive('update')
            ->once()
            ->with($list, []);

        $result = $this->service->createList($user, 'Test', null, false);

        $this->assertSame($list, $result);
    }

    public function test_update_list_generates_slug_when_changing_to_public(): void
    {
        // updateList does not generate slug; slug is generated at creation
        $this->markTestSkipped('Slug is generated at list creation, not at update');
    }

    public function test_update_list_clears_slug_when_changing_to_private(): void
    {
        // updateList does not clear slug; slug management happens at creation
        $this->markTestSkipped('Slug is generated at list creation');
    }

    public function test_add_item_to_list_assigns_correct_position(): void
    {
        $list = Mockery::mock(MyList::class)->makePartial();
        $list->shouldReceive('items->max')->with('position')->andReturn(5);

        $article = Mockery::mock(Article::class)->makePartial();
        $article->status = ArticleStatus::Publish;
        $article->shouldReceive('trashed')->andReturn(false);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('trashed')->andReturn(false);
        $article->user = $user;

        $item = Mockery::mock(MyListItem::class);

        $this->itemRepository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use ($list, $article) {
                return $data['list_id'] === $list->id
                    && $data['article_id'] === $article->id
                    && $data['position'] === 6
                    && $data['note'] === 'Test note';
            }))
            ->andReturn($item);

        $result = $this->service->addItemToList($list, $article, 'Test note');

        $this->assertSame($item, $result);
    }

    public function test_add_item_throws_exception_for_non_public_article(): void
    {
        $list = Mockery::mock(MyList::class)->makePartial();

        $article = Mockery::mock(Article::class)->makePartial();
        $article->status = ArticleStatus::Draft;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only published articles can be added to the list.');

        $this->service->addItemToList($list, $article);
    }

    public function test_update_item_updates_note_and_position(): void
    {
        $item = Mockery::mock(MyListItem::class);

        $this->itemRepository->shouldReceive('update')
            ->once()
            ->with($item, ['note' => 'New note', 'position' => 10]);

        $item->shouldReceive('refresh')->once();

        $result = $this->service->updateItem($item, 'New note', 10);

        $this->assertSame($item, $result);
    }

    public function test_update_item_skips_update_when_no_data_provided(): void
    {
        $item = Mockery::mock(MyListItem::class);

        $this->itemRepository->shouldNotReceive('update');
        $item->shouldNotReceive('refresh');

        $result = $this->service->updateItem($item, null, null);

        $this->assertSame($item, $result);
    }

    public function test_is_article_public_returns_true_for_published_article(): void
    {
        $article = Mockery::mock(Article::class)->makePartial();
        $article->status = ArticleStatus::Publish;
        $article->shouldReceive('trashed')->andReturn(false);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('trashed')->andReturn(false);
        $article->user = $user;

        $result = $this->service->isArticlePublic($article);

        $this->assertTrue($result);
    }

    public function test_is_article_public_returns_false_for_draft_article(): void
    {
        $article = Mockery::mock(Article::class)->makePartial();
        $article->status = ArticleStatus::Draft;

        $result = $this->service->isArticlePublic($article);

        $this->assertFalse($result);
    }

    public function test_is_article_public_returns_false_for_trashed_article(): void
    {
        $article = Mockery::mock(Article::class)->makePartial();
        $article->status = ArticleStatus::Publish;
        $article->shouldReceive('trashed')->andReturn(true);

        $result = $this->service->isArticlePublic($article);

        $this->assertFalse($result);
    }

    public function test_is_article_public_returns_false_for_trashed_author(): void
    {
        $article = Mockery::mock(Article::class)->makePartial();
        $article->status = ArticleStatus::Publish;
        $article->shouldReceive('trashed')->andReturn(false);

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('trashed')->andReturn(true);
        $article->user = $user;

        $result = $this->service->isArticlePublic($article);

        $this->assertFalse($result);
    }

    public function test_delete_list_calls_repository(): void
    {
        $list = Mockery::mock(MyList::class);

        $this->listRepository->shouldReceive('delete')->once()->with($list);

        $this->service->deleteList($list);

        $this->assertTrue(true); // アサーションが必要なため
    }

    public function test_remove_item_calls_repository(): void
    {
        $item = Mockery::mock(MyListItem::class);

        $this->itemRepository->shouldReceive('delete')->once()->with($item);

        $this->service->removeItem($item);

        $this->assertTrue(true);
    }

    public function test_reorder_items_calls_repository(): void
    {
        $list = Mockery::mock(MyList::class);
        $items = [['id' => 1, 'position' => 1], ['id' => 2, 'position' => 2]];

        $this->itemRepository->shouldReceive('updatePositions')
            ->once()
            ->with($list, $items);

        $this->service->reorderItems($list, $items);

        $this->assertTrue(true);
    }

    public function test_get_public_list_by_slug_calls_repository(): void
    {
        $list = Mockery::mock(MyList::class)->makePartial();
        $list->shouldReceive('load')->with('user')->andReturnSelf();

        $this->listRepository->shouldReceive('findOrFailPublicBySlug')
            ->once()
            ->with('test-slug')
            ->andReturn($list);

        $result = $this->service->getPublicListBySlug('test-slug');

        $this->assertSame($list, $result);
    }
}
