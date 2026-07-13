<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\MyListItemRepository;

use App\Models\MyList;
use App\Models\MyListItem;
use App\Repositories\MyListItemRepository;
use Tests\Feature\TestCase;

class UpdatePositionsTest extends TestCase
{
    private MyListItemRepository $repository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(MyListItemRepository::class);
    }

    public function test_指定したアイテムの位置を一括更新できる(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create();
        $item1 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 1]);
        $item2 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 2]);
        $item3 = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 3]);

        $this->repository->updatePositions($list, [
            ['id' => $item3->id, 'position' => 1],
            ['id' => $item2->id, 'position' => 2],
            ['id' => $item1->id, 'position' => 3],
        ]);

        $this->assertSame(1, $item3->fresh()->position);
        $this->assertSame(2, $item2->fresh()->position);
        $this->assertSame(3, $item1->fresh()->position);
    }

    public function test_他リストのアイテム_i_dを渡しても更新されない(): void
    {
        /** @var MyList $ownList */
        $ownList = MyList::factory()->create();
        /** @var MyList $otherList */
        $otherList = MyList::factory()->create();

        $ownItem = MyListItem::factory()->create(['list_id' => $ownList->id, 'position' => 1]);
        $otherItem = MyListItem::factory()->create(['list_id' => $otherList->id, 'position' => 1]);

        $this->repository->updatePositions($ownList, [
            ['id' => $ownItem->id, 'position' => 5],
            ['id' => $otherItem->id, 'position' => 9],
        ]);

        $this->assertSame(5, $ownItem->fresh()->position);
        // 他リストのアイテムは list_id が一致しないため更新されない
        $this->assertSame(1, $otherItem->fresh()->position);
    }

    public function test_空配列を渡した場合は何もしない(): void
    {
        /** @var MyList $list */
        $list = MyList::factory()->create();
        $item = MyListItem::factory()->create(['list_id' => $list->id, 'position' => 1]);

        $this->repository->updatePositions($list, []);

        $this->assertSame(1, $item->fresh()->position);
    }
}
