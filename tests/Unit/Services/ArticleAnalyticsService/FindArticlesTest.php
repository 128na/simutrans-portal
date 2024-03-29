<?php

declare(strict_types=1);

namespace Tests\Unit\Services\ArticleAnalyticsService;

use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Models\User;
use App\Repositories\ArticleRepository;
use App\Services\ArticleAnalyticsService;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class FindArticlesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getSUT(): ArticleAnalyticsService
    {
        return app(ArticleAnalyticsService::class);
    }

    #[DataProvider('data')]
    public function test($data, $expected): void
    {
        $user = new User();
        $request = new SearchRequest(['ids' => [1], ...$data]);
        $this->mock(ArticleRepository::class, function (MockInterface $m) use ($user, $expected) {
            $m->shouldReceive('findAllForAnalytics')
                ->withArgs([$user, [1], ...$expected])
                ->once()->andReturn(new Collection());
        });
        $result = $this->getSUT()->findArticles($user, $request);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public static function data()
    {
        yield 'daily' => [
            [
                'type' => 'daily',
                'start_date' => '2020-01-02',
                'end_date' => '2023-04-05',
            ],
            [
                1, ['20200102', '20230405'],
            ],
        ];
        yield 'monthly' => [
            [
                'type' => 'monthly',
                'start_date' => '2020-01-02',
                'end_date' => '2023-04-05',
            ],
            [
                2, ['202001', '202304'],
            ],
        ];
        yield 'yearly' => [
            [
                'type' => 'yearly',
                'start_date' => '2020-01-02',
                'end_date' => '2023-04-05',
            ],
            [
                3, ['2020', '2023'],
            ],
        ];
    }
}
