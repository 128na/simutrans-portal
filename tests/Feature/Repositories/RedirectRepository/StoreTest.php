<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\RedirectRepository;

use App\Models\Redirect;
use App\Models\User;
use App\Repositories\RedirectRepository;
use Tests\Feature\TestCase;

final class StoreTest extends TestCase
{
    private RedirectRepository $redirectRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->redirectRepository = app(RedirectRepository::class);
    }

    public function test_新しいリダイレクトを作成できる(): void
    {
        $user = User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'from' => '/old-path',
            'to' => '/new-path',
        ];

        $redirect = $this->redirectRepository->store($data);

        $this->assertInstanceOf(Redirect::class, $redirect);
        $this->assertSame($user->id, $redirect->user_id);
        $this->assertSame('/old-path', $redirect->from);
        $this->assertSame('/new-path', $redirect->to);
        $this->assertDatabaseHas('redirects', [
            'user_id' => $user->id,
            'from' => '/old-path',
            'to' => '/new-path',
        ]);
    }

    public function test_user_idなしでリダイレクトを作成できる(): void
    {
        $data = [
            'from' => '/old-path',
            'to' => '/new-path',
        ];

        $redirect = $this->redirectRepository->store($data);

        $this->assertInstanceOf(Redirect::class, $redirect);
        $this->assertNull($redirect->user_id);
        $this->assertSame('/old-path', $redirect->from);
        $this->assertSame('/new-path', $redirect->to);
    }
}
