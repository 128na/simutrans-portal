<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\RedirectRepository;

use App\Models\Redirect;
use App\Models\User;
use App\Repositories\RedirectRepository;
use Tests\Feature\TestCase;

final class FindByUserTest extends TestCase
{
    private RedirectRepository $redirectRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->redirectRepository = app(RedirectRepository::class);
    }

    public function test_ユーザー_i_dでリダイレクトを検索できる(): void
    {
        $user = User::factory()->create();
        $redirect1 = Redirect::factory()->create(['user_id' => $user->id]);
        $redirect2 = Redirect::factory()->create(['user_id' => $user->id]);
        $otherRedirect = Redirect::factory()->create(); // Different user

        $results = $this->redirectRepository->findByUser($user->id);

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($redirect1));
        $this->assertTrue($results->contains($redirect2));
        $this->assertFalse($results->contains($otherRedirect));
    }

    public function test_該当するリダイレクトがない場合は空のコレクションを返す(): void
    {
        $user = User::factory()->create();

        $results = $this->redirectRepository->findByUser($user->id);

        $this->assertCount(0, $results);
        $this->assertTrue($results->isEmpty());
    }

    public function test_複数ユーザーのリダイレクトが混在しても正しくフィルタリングできる(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $redirect1 = Redirect::factory()->create(['user_id' => $user1->id]);
        $redirect2 = Redirect::factory()->create(['user_id' => $user2->id]);
        $redirect3 = Redirect::factory()->create(['user_id' => $user1->id]);

        $results = $this->redirectRepository->findByUser($user1->id);

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($redirect1));
        $this->assertTrue($results->contains($redirect3));
        $this->assertFalse($results->contains($redirect2));
    }
}
