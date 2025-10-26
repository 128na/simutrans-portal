<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\Tag;

use App\Http\Requests\Api\Tag\StoreRequest;
use App\Models\Tag;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class StoreRequestTest extends TestCase
{
    private Tag $tag;
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->tag = Tag::factory()->create();
    }

    #[DataProvider('dataFail')]
    public function test_fail(Closure $setup, string $expectedErrorField): void
    {
        $data = $setup($this);
        $messageBag = $this->makeValidator(StoreRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    #[DataProvider('dataPass')]
    public function test_pass(Closure $setup): void
    {
        $data = $setup($this);
        $messageBag = $this->makeValidator(StoreRequest::class, $data)->errors();
        $this->assertEmpty($messageBag->toArray());
    }

    public static function dataFail(): \Generator
    {
        yield 'nameがnull' => [fn(self $self): array => ['name' => null], 'name'];
        yield 'nameが21文字以上' => [fn(self $self): array => ['name' => str_repeat('a', 21)], 'name'];
        yield 'nameが存在する' => [fn(self $self): array => ['name' => $self->tag->name], 'name'];
    }

    public static function dataPass(): \Generator
    {
        yield '成功' => [fn(self $self): array => ['name' => 'new_tag']];
    }
}
