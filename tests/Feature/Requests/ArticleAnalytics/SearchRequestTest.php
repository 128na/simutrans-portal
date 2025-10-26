<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\ArticleAnalytics;

use App\Http\Requests\Api\ArticleAnalytics\SearchRequest;
use App\Models\Article;
use App\Models\User;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class SearchRequestTest extends TestCase
{
    private User $user;
    private Article $othersArticle;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->othersArticle = Article::factory()->create([]);
    }

    #[DataProvider('dataValidation')]
    public function test(Closure $setup, string $expectedErrorField): void
    {
        $this->actingAs($this->user);
        $data = $setup($this);
        $messageBag = $this->makeValidator(SearchRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    public static function dataValidation(): \Generator
    {
        yield 'idsがnull' => [fn(self $self): array => ['ids' => null], 'ids'];
        yield 'idsが空' => [fn(self $self): array => ['ids' => []], 'ids'];

        yield 'ids.0が存在しないID' => [fn(self $self): array => ['ids' => [99999]], 'ids.0'];
        yield 'ids.0が他人の記事' => [fn(self $self): array => ['ids' => [$self->othersArticle->id]], 'ids.0'];

        yield 'typeがnull' => [fn(self $self): array => ['type' => null], 'type'];
        yield 'typeが不正' => [fn(self $self): array => ['type' => 'invalid-type'], 'type'];

        yield 'start_dateがnull' => [fn(self $self): array => ['start_date' => null], 'start_date'];
        yield 'start_dateが不正' => [fn(self $self): array => ['start_date' => 'invalid-start_date'], 'start_date'];

        yield 'end_dateがnull' => [fn(self $self): array => ['end_date' => null], 'end_date'];
        yield 'end_dateが不正' => [fn(self $self): array => ['end_date' => 'invalid-start_date'], 'end_date'];
        yield 'end_dateがstart_dateよりも過去' => [fn(self $self): array => ['start_date' => now(), 'end_date' => now()->modify('-1 day')], 'end_date'];
    }
}
