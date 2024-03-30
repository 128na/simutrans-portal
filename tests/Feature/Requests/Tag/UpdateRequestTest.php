<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\Tag;

use App\Http\Requests\Api\Tag\UpdateRequest;
use App\Models\Tag;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class UpdateRequestTest extends TestCase
{
    private Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tag = Tag::factory()->create();

    }

    #[DataProvider('dataFail')]
    public function testFail(Closure $setup, string $expectedErrorField): void
    {
        $data = $setup($this);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    #[DataProvider('dataPass')]
    public function testPass(Closure $setup): void
    {
        $data = $setup($this);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertCount(0, $messageBag->toArray());
    }

    public static function dataFail(): \Generator
    {
        yield 'descriptionが1025文字以上' => [fn (): array => ['description' => str_repeat('a', 1025)], 'description'];
    }

    public static function dataPass(): \Generator
    {
        yield 'descriptionが1024文字以下' => [fn (): array => ['description' => str_repeat('a', 1024)]];
    }
}
