<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\NotJustNumbers;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

final class NotJustNumbersTest extends TestCase
{
    private Closure $failClosure;

    private bool $failCalled = false;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->failCalled = false;

        $mock = $this->mock(PotentiallyTranslatedString::class, fn(MockInterface $mock) => $mock->allows()->translate());
        $this->failClosure = function () use ($mock) {
            $this->failCalled = true;

            return $mock;
        };
    }

    #[Test]
    #[DataProvider('data')]
    public function test(string $value, bool $expected): void
    {
        $this->getSUT()->validate('dummy', $value, $this->failClosure);
        $this->assertSame($expected, $this->failCalled);
    }

    public static function data(): \Generator
    {
        yield '数字のみ' => ['1', true];
        yield '数字と英字' => ['1a', false];
        yield '16進数' => ['0x11', false];
    }

    private function getSUT(): NotJustNumbers
    {
        return new NotJustNumbers();
    }
}
