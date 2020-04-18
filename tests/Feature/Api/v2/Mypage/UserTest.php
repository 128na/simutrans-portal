<?php

namespace Tests\Feature\Api\v2\Mypage;

use App\Models\Attachment;
use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    public function testIndex()
    {
        $user = factory(User::class)->create();
        $url = route('api.v2.users.index');

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $res = $this->getJson($url);
        $res->assertOK();
        $res->assertJson(['data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile' => [
                'id' => $user->profile->id,
                'data' => [
                    'avatar' => $user->profile->data->avatar,
                    'description' => $user->profile->data->description,
                    'twitter' => $user->profile->data->twitter,
                    'website' => $user->profile->data->website,
                ],
            ],
            'admin' => $user->isAdmin(),
            'verified' => !!$user->email_verified_at,
            'attachments' => [],
        ]]);
    }

    public function testStore()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'profile' => [
                'data' => [
                    'avatar' => $user->profile->data->avatar,
                    'description' => $user->profile->data->description,
                    'twitter' => $user->profile->data->twitter,
                    'website' => $user->profile->data->website,
                ],
            ],
        ];

        $url = route('api.v2.users.update');

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($user);

        $res = $this->postJson($url, ['user' => array_merge($data, ['name' => null])]);
        $res->assertJsonValidationErrors(['user.name']);
        $res = $this->postJson($url, ['user' => array_merge($data, ['name' => str_repeat('a', 256)])]);
        $res->assertJsonValidationErrors(['user.name']);
        $other_user = factory(User::class)->create();
        $res = $this->postJson($url, ['user' => array_merge($data, ['name' => $other_user->name])]);
        $res->assertJsonValidationErrors(['user.name']);

        $res = $this->postJson($url, ['user' => array_merge($data, ['email' => null])]);
        $res->assertJsonValidationErrors(['user.email']);
        $res = $this->postJson($url, ['user' => array_merge($data, ['email' => 'invalid-email'])]);
        $res->assertJsonValidationErrors(['user.email']);
        $other_user = factory(User::class)->create();
        $res = $this->postJson($url, ['user' => array_merge($data, ['email' => $other_user->email])]);
        $res->assertJsonValidationErrors(['user.email']);

        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => null])]);
        $res->assertJsonValidationErrors(['user.profile']);

        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => ['data' => null]])]);
        $res->assertJsonValidationErrors(['user.profile.data']);

        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => ['data' => ['avatar' => 99999]]])]);
        $res->assertJsonValidationErrors(['user.profile.data.avatar']);
        $not_image = Attachment::createFromFile(UploadedFile::fake()->create('not_image.zip', 1), $user->id);
        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => ['data' => ['avatar' => $not_image->id]]])]);
        $res->assertJsonValidationErrors(['user.profile.data.avatar']);
        $other_user = factory(User::class)->create();
        $other_avatar = Attachment::createFromFile(UploadedFile::fake()->image('avatar.jpg', 1), $other_user->id);
        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => ['data' => ['avatar' => $other_avatar->id]]])]);
        $res->assertJsonValidationErrors(['user.profile.data.avatar']);

        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => ['data' => ['description' => str_repeat('a', 256)]]])]);
        $res->assertJsonValidationErrors(['user.profile.data.description']);
        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => ['data' => ['website' => 'invalid-url']]])]);
        $res->assertJsonValidationErrors(['user.profile.data.website']);
        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => ['data' => ['website' => 'http://example.com/' . str_repeat('a', 256)]]])]);
        $res->assertJsonValidationErrors(['user.profile.data.website']);
        $res = $this->postJson($url, ['user' => array_merge($data, ['profile' => ['data' => ['twitter' => str_repeat('a', 256)]]])]);
        $res->assertJsonValidationErrors(['user.profile.data.twitter']);

        $res = $this->postJson($url, ['user' => $data]);
        $res->assertOK();
        $res->assertJson(['data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile' => [
                'id' => $user->profile->id,
                'data' => [
                    'avatar' => $user->profile->data->avatar,
                    'description' => $user->profile->data->description,
                    'twitter' => $user->profile->data->twitter,
                    'website' => $user->profile->data->website,
                ],
            ],
            'admin' => $user->isAdmin(),
            'verified' => !!$user->email_verified_at,
            'attachments' => [],
        ]]);

        Notification::assertNothingSent();
    }

    public function testEmailChange()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $new_email = 'new@example.com';
        $data = [
            'name' => $user->name,
            'email' => $new_email,
            'profile' => [
                'data' => [
                    'avatar' => $user->profile->data->avatar,
                    'description' => $user->profile->data->description,
                    'twitter' => $user->profile->data->twitter,
                    'website' => $user->profile->data->website,
                ],
            ],
        ];
        $this->actingAs($user);

        $url = route('api.v2.users.update');
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
                    'twitter' => $user->profile->data->twitter,
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
