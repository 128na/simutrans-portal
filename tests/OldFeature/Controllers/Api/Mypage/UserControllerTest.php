<?php

declare(strict_types=1);

namespace Tests\OldFeature\Controllers\Api\Mypage;

use App\Models\Attachment;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Tests\ArticleTestCase;

class UserControllerTest extends ArticleTestCase
{
    private Attachment $not_image;

    private Attachment $user2_avatar;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user2->fill(['email' => 'other@example.com', 'name' => 'other name'])->save();
        $this->not_image = $this->createFromFile(UploadedFile::fake()->create('not_image.zip', 1), $this->user->id);
        $this->user2_avatar = $this->createFromFile(UploadedFile::fake()->image('avatar.jpg', 1), $this->user2->id);
    }

    public function testIndex(): void
    {
        $url = '/api/mypage/user';

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);

        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertJson(['data' => [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'nickname' => $this->user->nickname,
            'profile' => [
                'id' => $this->user->profile->id,
                'data' => [
                    'avatar' => $this->user->profile->data->avatar,
                    'description' => $this->user->profile->data->description,
                ],
            ],
            'admin' => $this->user->isAdmin(),
            'verified' => (bool) $this->user->email_verified_at,
            'attachments' => [],
        ]]);
    }

    /**
     * @dataProvider dataValidation
     */
    public function testStore(Closure $data, ?string $error_field): void
    {
        Notification::fake();

        $data = array_merge([
            'name' => $this->user->name,
            'email' => $this->user->email,
            'nickname' => $this->user->nickname,
            'profile' => [
                'data' => [
                    'avatar' => $this->user->profile->data->avatar,
                    'description' => $this->user->profile->data->description,
                ],
            ],
        ], Closure::bind($data, $this)());

        $url = '/api/mypage/user';

        $this->actingAs($this->user);

        $res = $this->postJson($url, ['user' => $data]);

        if (is_null($error_field)) {
            $res->assertOK();
            $res->assertJson(['data' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'nickname' => $this->user->nickname,
                'profile' => [
                    'id' => $this->user->profile->id,
                    'data' => [
                        'avatar' => $this->user->profile->data->avatar,
                        'description' => $this->user->profile->data->description,
                    ],
                ],
                'admin' => $this->user->isAdmin(),
                'verified' => (bool) $this->user->email_verified_at,
                'attachments' => [],
            ]]);
        } else {
            $res->assertJsonValidationErrors($error_field);
        }

        Notification::assertNothingSent();
    }

    public static function dataValidation(): \Generator
    {
        yield 'user.nameがnull' => [
            fn (): array => ['name' => null], 'user.name',
        ];
        yield 'user.nameが256文字以上' => [
            fn (): array => ['name' => str_repeat('a', 256)], 'user.name',
        ];
        yield 'user.nameが存在する' => [
            fn (): array => ['name' => 'other name'], 'user.name',
        ];

        yield 'user.emailがnull' => [
            fn (): array => ['email' => null], 'user.email',
        ];
        yield 'user.emailが不正' => [
            fn (): array => ['email' => 'invalid-email'], 'user.email',
        ];
        yield 'user.emailが存在する' => [
            fn (): array => ['email' => 'other@example.com'], 'user.email',
        ];

        yield 'user.profileがnull' => [
            fn (): array => ['profile' => null], 'user.profile',
        ];

        yield 'user.profile.dataがnull' => [
            fn (): array => ['profile' => ['data' => null]], 'user.profile.data',
        ];

        yield 'user.profile.data.avatarが存在しない' => [
            fn (): array => ['profile' => ['data' => ['avatar' => 99999]]], 'user.profile.data.avatar',
        ];
        yield 'user.profile.data.avatarが画像以外' => [
            fn (): array => ['profile' => ['data' => ['avatar' => $this->not_image->id]]], 'user.profile.data.avatar',
        ];
        yield 'user.profile.data.avatarが他人のアップロードした画像' => [
            fn (): array => ['profile' => ['data' => ['avatar' => $this->user2_avatar->id]]], 'user.profile.data.avatar',
        ];

        yield 'user.profile.data.descriptionが1025文字以上' => [
            fn (): array => ['profile' => ['data' => ['description' => str_repeat('a', 1025)]]], 'user.profile.data.description',
        ];
        yield 'user.profile.data.websiteが不正' => [
            fn (): array => ['profile' => ['data' => ['website' => 'invalid-url']]], 'user.profile.data.website',
        ];
        yield 'user.profile.data.websiteが256文字以上' => [
            fn (): array => ['profile' => ['data' => ['website' => 'http://example.com/'.str_repeat('a', 256)]]], 'user.profile.data.website',
        ];
    }

    public function testEmailChange(): void
    {
        Notification::fake();

        /** @var User */
        $user = User::factory()->create();

        $new_email = 'new@example.com';
        $data = [
            'name' => $user->name,
            'email' => $new_email,
            'profile' => [
                'data' => [
                    'avatar' => $user->profile->data->avatar,
                    'description' => $user->profile->data->description,
                    'website' => $user->profile->data->website,
                ],
            ],
        ];
        $this->actingAs($user);

        $url = '/api/mypage/user';
        $res = $this->postJson($url, ['user' => $data]);
        $res->assertJson(['data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile' => [
                'id' => $user->profile->id,
                'data' => [
                    'avatar' => $user->profile->data->avatar,
                    'description' => $user->profile->data->description,
                    'website' => $user->profile->data->website,
                ],
            ],
            'admin' => $user->isAdmin(),
            'verified' => false,
            'attachments' => [],
        ]]);

        $user->fresh();

        $this->assertEquals($user->email, $new_email);

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
