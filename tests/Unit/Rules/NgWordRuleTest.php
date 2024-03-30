<?php

namespace Tests\Unit\Rules;

use App\Rules\NgWordRule;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class NgWordRuleTest extends TestCase
{
    private Closure $failClosure;

    private bool $failCalled = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->failCalled = false;

        $mock = $this->mock(PotentiallyTranslatedString::class, fn (MockInterface $mock) => $mock->allows('translate'));
        $this->failClosure = function () use ($mock) {
            $this->failCalled = true;

            return $mock;
        };
    }

    private function getSUT(array $ngWords): NgWordRule
    {
        return new NgWordRule($ngWords);
    }

    #[Test]
    #[DataProvider('data')]
    public function test(array $ngWords, string $value, bool $expected): void
    {
        $this->getSUT($ngWords)
            ->validate('dummy', $value, $this->failClosure);
        $this->assertEquals($expected, $this->failCalled);
    }

    public static function data(): \Generator
    {
        yield 'ok' => [['@'], 'test', false];
        yield '1個マッチ' => [['@'], 'test@example', true];
        yield '複数個マッチ' => [['@'], 'test@ex@mple', true];
        yield '複数種類マッチ' => [['@', '#'], '#test@example', true];
    }
}
