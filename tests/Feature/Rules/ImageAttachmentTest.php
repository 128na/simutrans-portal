<?php

declare(strict_types=1);

namespace Tests\Feature\Rules;

use App\Models\User;
use App\Rules\ImageAttachment;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\TestCase;

final class ImageAttachmentTest extends TestCase
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
    public function test(Closure $setup, bool $expected): void
    {
        $value = $setup($this);
        $this->getSUT()
            ->validate('dummy', $value, $this->failClosure);
        $this->assertSame($expected, $this->failCalled);
    }

    public static function data(): \Generator
    {
        yield 'ok' => [fn (self $self) => $self->createImageAttachment(User::factory()->create())->id, false];
        yield 'ng' => [fn (self $self) => $self->createAttachment(User::factory()->create())->id, true];
    }

    private function getSUT(): ImageAttachment
    {
        return app(ImageAttachment::class);
    }
}
