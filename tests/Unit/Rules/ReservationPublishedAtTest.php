<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\ReservationPublishedAt;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

final class ReservationPublishedAtTest extends TestCase
{
    private Closure $failClosure;

    private bool $failCalled = false;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->failCalled = false;

        $mock = $this->mock(PotentiallyTranslatedString::class, fn (MockInterface $mock) => $mock->allows()->translate());
        $this->failClosure = function () use ($mock) {
            $this->failCalled = true;

            return $mock;
        };
    }

    #[Test]
    #[DataProvider('data')]
    public function test(string $value, array $data, bool $expected): void
    {
        $this->getSUT()
            ->setData($data)
            ->validate('dummy', $value, $this->failClosure);
        $this->assertSame($expected, $this->failCalled);
    }

    public static function data(): \Generator
    {
        yield '予約でない' => ['', ['article' => ['status' => 'publish']], false];
        yield '予約で1時間以内' => ['2020-01-02T03:04:05.000+09:00', ['article' => ['status' => 'reservation']], true];
        yield '予約で1時間より先' => ['2020-01-02T04:04:05.000+09:00', ['article' => ['status' => 'reservation']], false];
    }

    private function getSUT(): ReservationPublishedAt
    {
        return new ReservationPublishedAt(CarbonImmutable::create(2020, 01, 02, 03, 04, 05));
    }
}
