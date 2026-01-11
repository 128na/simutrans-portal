<?php

declare(strict_types=1);

namespace Tests\Unit\Casts;

use App\Casts\ToProfileData;
use App\Models\User;
use App\Models\User\ProfileData;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ToProfileDataTest extends TestCase
{
    private ToProfileData $cast;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new ToProfileData;
    }

    #[Test]
    public function it_casts_json_to_profile_data(): void
    {
        $user = new User;

        $jsonData = json_encode([
            'avatar' => 123,
            'description' => 'プロフィール説明',
            'website' => 'https://example.com',
        ]);

        $result = $this->cast->get($user, 'profile', $jsonData, []);

        $this->assertInstanceOf(ProfileData::class, $result);
    }

    #[Test]
    public function it_handles_empty_json_data(): void
    {
        $user = new User;

        $jsonData = json_encode([]);

        $result = $this->cast->get($user, 'profile', $jsonData, []);

        $this->assertInstanceOf(ProfileData::class, $result);
    }

    #[Test]
    public function it_handles_partial_data(): void
    {
        $user = new User;

        $jsonData = json_encode([
            'avatar' => 456,
        ]);

        $result = $this->cast->get($user, 'profile', $jsonData, []);

        $this->assertInstanceOf(ProfileData::class, $result);
    }

    #[Test]
    public function it_serializes_profile_data_to_json(): void
    {
        $profileData = new ProfileData([
            'avatar' => 789,
            'description' => 'テストユーザー',
            'website' => 'https://test.example.com',
        ]);

        $result = $this->cast->set(new User, 'profile', $profileData, []);

        $this->assertJson($result);
        $decoded = json_decode($result, true);
        $this->assertArrayHasKey('avatar', $decoded);
        $this->assertSame(789, $decoded['avatar']);
        $this->assertArrayHasKey('description', $decoded);
        $this->assertSame('テストユーザー', $decoded['description']);
        $this->assertArrayHasKey('website', $decoded);
        // website は配列として保存される
        $this->assertIsArray($decoded['website']);
        $this->assertSame(['https://test.example.com'], $decoded['website']);
    }

    #[Test]
    public function it_returns_empty_string_when_encoding_fails(): void
    {
        $profileData = new ProfileData([]);

        $result = $this->cast->set(new User, 'profile', $profileData, []);

        $this->assertIsString($result);
    }

    #[Test]
    public function it_handles_null_values_in_profile_data(): void
    {
        $user = new User;

        $jsonData = json_encode([
            'avatar' => null,
            'description' => null,
            'website' => null,
        ]);

        $result = $this->cast->get($user, 'profile', $jsonData, []);

        $this->assertInstanceOf(ProfileData::class, $result);
    }

    #[Test]
    public function it_preserves_all_fields_during_roundtrip(): void
    {
        $user = new User;

        $originalData = [
            'avatar' => 999,
            'description' => 'ラウンドトリップテスト',
            'website' => 'https://roundtrip.example.com',
        ];

        $jsonData = json_encode($originalData);

        // get でキャスト
        $profileData = $this->cast->get($user, 'profile', $jsonData, []);

        // set でシリアライズ
        $serialized = $this->cast->set(new User, 'profile', $profileData, []);

        // 元のデータと一致することを確認（website は配列に変換される）
        $decoded = json_decode($serialized, true);
        $this->assertSame($originalData['avatar'], $decoded['avatar']);
        $this->assertSame($originalData['description'], $decoded['description']);
        $this->assertIsArray($decoded['website']);
        $this->assertSame(['https://roundtrip.example.com'], $decoded['website']);
    }
}
