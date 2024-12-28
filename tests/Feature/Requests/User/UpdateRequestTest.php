<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\User;

use App\Http\Requests\Api\User\UpdateRequest;
use App\Models\User;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class UpdateRequestTest extends TestCase
{
    public User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[DataProvider('dataValidation')]
    public function test(Closure $setup, string $expectedErrorField): void
    {
        $data = $setup($this);
        $messageBag = $this->makeValidator(UpdateRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    public static function dataValidation(): \Generator
    {
        yield 'user.nameがnull' => [
            fn (self $self): array => ['user' => [
                'name' => null,
            ]],
            'user.name',
        ];
        yield 'user.nameが256文字以上' => [
            fn (self $self): array => ['user' => [
                'name' => str_repeat('a', 256),
            ]],
            'user.name',
        ];
        yield 'user.nameが存在する' => [
            fn (self $self): array => ['user' => [
                'name' => $self->user->name,
            ]],
            'user.name',
        ];

        yield 'user.emailがnull' => [
            fn (self $self): array => ['user' => [
                'email' => null,
            ]],
            'user.email',
        ];
        yield 'user.emailが不正' => [
            fn (self $self): array => ['user' => [
                'email' => 'invalid-email',
            ]],
            'user.email',
        ];
        yield 'user.emailが存在する' => [
            fn (self $self): array => ['user' => [
                'email' => $self->user->email,
            ]],
            'user.email',
        ];

        yield 'user.profileがnull' => [
            fn (self $self): array => ['user' => [
                'profile' => null,
            ]],
            'user.profile',
        ];

        yield 'user.profile.dataがnull' => [
            fn (self $self): array => ['user' => [
                'profile' => ['data' => null],
            ]],
            'user.profile.data',
        ];

        yield 'user.profile.data.avatarが存在しない' => [
            fn (self $self): array => ['user' => [
                'profile' => ['data' => ['avatar' => 99999]],
            ]],
            'user.profile.data.avatar',
        ];
        yield 'user.profile.data.avatarが画像以外' => [
            fn (self $self): array => ['user' => [
                'profile' => ['data' => ['avatar' => $self->createAttachment($self->user)->id]],
            ]],
            'user.profile.data.avatar',
        ];
        yield 'user.profile.data.avatarが他人のアップロードした画像' => [
            fn (self $self): array => ['user' => [
                'profile' => ['data' => ['avatar' => $self->createAttachment(User::factory()->create())->id]],
            ]],
            'user.profile.data.avatar',
        ];

        yield 'user.profile.data.descriptionが1025文字以上' => [
            fn (self $self): array => ['user' => [
                'profile' => ['data' => ['description' => str_repeat('a', 1025)]],
            ]],
            'user.profile.data.description',
        ];
        yield 'user.profile.data.websiteが不正' => [
            fn (self $self): array => [
                'user' => [
                    'profile' => ['data' => ['website' => 'invalid-url']],
                ],
            ],
            'user.profile.data.website',
        ];
        yield 'user.profile.data.websiteが256文字以上' => [
            fn (self $self): array => ['user' => [
                'profile' => ['data' => ['website' => 'http://example.com/'.str_repeat('a', 256)]],
            ]],
            'user.profile.data.website',
        ];
    }
}
