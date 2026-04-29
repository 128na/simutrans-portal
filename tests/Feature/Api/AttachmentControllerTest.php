<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Tests\Feature\TestCase;

class AttachmentControllerTest extends TestCase
{
    public function test_index_requires_auth(): void
    {
        $this->getJson('/api/v1/attachments')->assertUnauthorized();
    }

    public function test_index_returns_user_attachments(): void
    {
        $user = User::factory()->create();
        $this->createAttachment($user);
        $this->createImageAttachment($user);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/attachments');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure(['data' => [['id', 'original_name', 'is_image', 'created_at']]]);
    }

    public function test_index_excludes_other_users_attachments(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->createAttachment($other);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/attachments');

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_index_distinguishes_image_from_file(): void
    {
        $user = User::factory()->create();
        $this->createAttachment($user);
        $this->createImageAttachment($user);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/attachments');

        $response->assertOk();
        $data = $response->json('data');
        $types = array_column($data, 'is_image');
        $this->assertContains(true, $types);
        $this->assertContains(false, $types);
    }

    public function test_index_returns_empty_when_no_attachments(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/attachments');

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
