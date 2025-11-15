<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Analytics;

use App\Actions\Analytics\FindArticles;
use App\Enums\ArticleAnalyticsType;
use App\Http\Requests\ArticleAnalytics\SearchRequest;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

final class FindArticlesTest extends TestCase
{
    #[DataProvider('data')]
    public function test(array $data, array $expected): void
    {
        $user = new User;
        $searchRequest = new SearchRequest(['ids' => [1], ...$data]);
        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($user, $expected): void {
            $mock->expects()->findAllForAnalytics($user, [1], ...$expected)->once()->andReturn(new Collection);
        });
        $result = $this->getSUT()($user, $searchRequest);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public static function data(): \Generator
    {
        yield 'daily' => [
            [
                'type' => 'daily',
                'start_date' => '2020-01-02',
                'end_date' => '2023-04-05',
            ],
            [
                ArticleAnalyticsType::Daily,
                ['20200102', '20230405'],
            ],
        ];
        yield 'monthly' => [
            [
                'type' => 'monthly',
                'start_date' => '2020-01-02',
                'end_date' => '2023-04-05',
            ],
            [
                ArticleAnalyticsType::Monthly,
                ['202001', '202304'],
            ],
        ];
        yield 'yearly' => [
            [
                'type' => 'yearly',
                'start_date' => '2020-01-02',
                'end_date' => '2023-04-05',
            ],
            [
                ArticleAnalyticsType::Yearly,
                ['2020', '2023'],
            ],
        ];
    }

    private function getSUT(): FindArticles
    {
        return app(FindArticles::class);
    }
}
