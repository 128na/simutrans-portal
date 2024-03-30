<?php

namespace Tests\Feature\Rules;

use App\Models\User;
use App\Rules\UniqueSlugByUser;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\TestCase;

class UniqueSlugByUserTest extends TestCase
{
    private User $user;

    private Closure $failClosure;

    private bool $failCalled = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->failCalled = false;

        $mock = $this->mock(PotentiallyTranslatedString::class, fn (MockInterface $mock) => $mock->allows('translate'));
        $this->failClosure = function () use ($mock) {
            $this->failCalled = true;

            return $mock;
        };
    }

    private function getSUT(): UniqueSlugByUser
    {
        return new UniqueSlugByUser();
    }

    #[Test]
    #[DataProvider('data')]
    public function test(Closure $setup, bool $expected): void
    {
        $value = $setup($this);
        $this->actingAs($this->user);
        $this->getSUT()
            ->validate('dummy', $value, $this->failClosure);
        $this->assertEquals($expected, $this->failCalled);
    }

    public static function data(): \Generator
    {
        yield 'ok' => [fn (self $self) => $self->createImageAttachment(User::factory()->create())->id, false];
        yield 'ng' => [fn (self $self) => $self->createAttachment(User::factory()->create())->id, true];
    }
}
