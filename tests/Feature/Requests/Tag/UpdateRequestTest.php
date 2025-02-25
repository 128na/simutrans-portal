<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\Tag;

use App\Http\Requests\Api\Tag\UpdateRequest;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class UpdateRequestTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

    }

    #[DataProvider('dataFail')]
    public function test_fail(Closure $setup, string $expectedErrorField): void
    {
        $data = $setup($this);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    #[DataProvider('dataPass')]
    public function test_pass(Closure $setup): void
    {
        $data = $setup($this);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertEmpty($messageBag->toArray());
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
