<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\User;

use App\Http\Requests\User\InviteRequest;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class InviteRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['email' => 'test@example.com']);
    }

    #[DataProvider('dataValidation')]
    public function test(array $data, string $expectedErrorField): void
    {
        $messageBag = $this->makeValidator(InviteRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    public static function dataValidation(): \Generator
    {
        yield 'nameが空' => [
            ['name' => ''], 'name',
        ];
        yield 'nameが101文字以上' => [
            ['name' => str_repeat('a', 101)], 'name',
        ];
        yield 'emailが空' => [
            ['email' => ''], 'email',
        ];
        yield 'emailが不正' => [
            ['email' => 'a'], 'email',
        ];
        yield 'emailが重複' => [
            ['email' => 'test@example.com'], 'email',
        ];
        yield 'passwordが空' => [
            ['password' => ''], 'password',
        ];
        yield 'passwordが10文字以下' => [
            ['password' => str_repeat('a', 10)], 'password',
        ];
    }
}
