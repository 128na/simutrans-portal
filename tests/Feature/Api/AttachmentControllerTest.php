<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

class AttachmentControllerTest extends TestCase
{
    #[DataProvider('authenticationModes')]
    public function test_external_attachment_index_is_unavailable(string $authentication): void
    {
        $user = User::factory()->create();
        $this->createAttachment($user);

        if ($authentication === 'session') {
            $this->actingAs($user, 'web');
        } elseif ($authentication === 'token') {
            $this->withToken($user->createToken('Legacy API')->plainTextToken);
        }

        $this->getJson('/api/v1/attachments')->assertNotFound();
    }

    /** @return array<string, array{string}> */
    public static function authenticationModes(): array
    {
        return ['guest' => ['guest'], 'session' => ['session'], 'token' => ['token']];
    }
}
