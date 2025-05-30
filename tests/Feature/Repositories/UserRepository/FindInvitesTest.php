<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\TestCase;

final class FindInvitesTest extends TestCase
{
    private User $user;

    private UserRepository $userRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
        $this->user = User::factory()->create();
    }

    public function test(): void
    {
        User::factory()->create(['invited_by' => $this->user->id]);
        $res = $this->userRepository->findInvites($this->user);

        $this->assertInstanceOf(Collection::class, $res, 'ユーザーが取得できること');
        $this->assertCount(1, $res);
    }

    public function test対象無し(): void
    {
        $this->user->delete();
        $res = $this->userRepository->findInvites($this->user);

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEmpty($res);
    }
}
