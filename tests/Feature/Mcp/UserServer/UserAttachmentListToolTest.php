<?php

declare(strict_types=1);

namespace Tests\Feature\MCP\UserServer;

use App\Mcp\Servers\SimutransAddonPortalUserServer;
use App\Mcp\Tools\UserAttachmentListTool;
use App\Models\User;
use Tests\Feature\TestCase;

class UserAttachmentListToolTest extends TestCase
{
    public function test_returns_user_attachments(): void
    {
        $user = User::factory()->create();
        $this->createAttachment($user);
        $this->createImageAttachment($user);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserAttachmentListTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertSee('original_name');
        $response->assertSee('is_image');
    }

    public function test_excludes_other_users_attachments(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->createAttachment($other);

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserAttachmentListTool::class, []);

        $response->assertOk()->assertHasNoErrors();
        $response->assertDontSee('file.zip');
    }

    public function test_returns_empty_for_user_with_no_attachments(): void
    {
        $user = User::factory()->create();

        $response = SimutransAddonPortalUserServer::actingAs($user)
            ->tool(UserAttachmentListTool::class, []);

        $response->assertOk()->assertHasNoErrors();
    }
}
