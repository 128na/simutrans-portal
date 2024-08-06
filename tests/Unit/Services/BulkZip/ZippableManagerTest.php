<?php

declare(strict_types=1);

namespace Tests\Unit\Services\BulkZip;

use App\Models\BulkZip;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Services\BulkZip\ZippableManager;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;
use stdClass;
use Tests\Unit\TestCase;

final class ZippableManagerTest extends TestCase
{
    public function testUser(): void
    {
        $user = new User;
        $this->mock(BulkZip::class, function (MockInterface $mock) use ($user): void {
            $mock->expects()->getAttribute('bulkZippable')->twice()->andReturn($user);
        });

        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user): void {
            $mock->expects()->findAllByUser($user, [])->once()->andReturn(new Collection);
        });

        $res = $this->getSUT()->getItems(app(BulkZip::class));
        $this->assertCount(0, $res);
    }

    public function test未対応モデル(): void
    {
        $this->mock(BulkZip::class, function (MockInterface $mock): void {
            $mock->expects()->getAttribute('bulkZippable')->once()->andReturn(stdClass::class);
        });

        $this->expectException(Exception::class);

        $this->getSUT()->getItems(app(BulkZip::class));
    }

    private function getSUT(): ZippableManager
    {
        return app(ZippableManager::class);
    }
}
