<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

class FindIncompleteMFAUsersTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository(new User);
    }

    /**
     * @test
     * MFA設定未完了のユーザー (15分以上経過) を取得する
     */
    public function test_finds_users_with_incomplete_mfa_after15_minutes(): void
    {
        // 15分以上前に更新された未完了MFAユーザー
        $incompleteUser = User::factory()->create([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_confirmed_at' => null,
            'updated_at' => now()->subMinutes(20),
        ]);

        $result = $this->repository->findIncompleteMFAUsers();

        $this->assertCount(1, $result);
        $this->assertTrue($result->contains($incompleteUser));
    }

    /**
     * @test
     * MFA設定完了済みユーザーは取得しない
     */
    public function test_does_not_find_users_with_completed_mfa(): void
    {
        User::factory()->create([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_confirmed_at' => now(),
            'updated_at' => now()->subMinutes(20),
        ]);

        $result = $this->repository->findIncompleteMFAUsers();

        $this->assertCount(0, $result);
    }

    /**
     * @test
     * MFA設定がないユーザーは取得しない
     */
    public function test_does_not_find_users_without_mfa(): void
    {
        User::factory()->create([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'updated_at' => now()->subMinutes(20),
        ]);

        $result = $this->repository->findIncompleteMFAUsers();

        $this->assertCount(0, $result);
    }

    /**
     * @test
     * 15分以内に更新されたユーザーは取得しない
     */
    public function test_does_not_find_recently_updated_users(): void
    {
        User::factory()->create([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_confirmed_at' => null,
            'updated_at' => now()->subMinutes(10),
        ]);

        $result = $this->repository->findIncompleteMFAUsers();

        $this->assertCount(0, $result);
    }

    /**
     * @test
     * 複数の条件に合致するユーザーを取得する
     */
    public function test_finds_multiple_incomplete_users(): void
    {
        $user1 = User::factory()->create([
            'two_factor_secret' => encrypt('secret1'),
            'two_factor_confirmed_at' => null,
            'updated_at' => now()->subMinutes(20),
        ]);

        $user2 = User::factory()->create([
            'two_factor_secret' => encrypt('secret2'),
            'two_factor_confirmed_at' => null,
            'updated_at' => now()->subMinutes(30),
        ]);

        // 除外されるユーザー (confirmed済み)
        User::factory()->create([
            'two_factor_secret' => encrypt('secret3'),
            'two_factor_confirmed_at' => now(),
            'updated_at' => now()->subMinutes(25),
        ]);

        $result = $this->repository->findIncompleteMFAUsers();

        $this->assertCount(2, $result);
        $this->assertTrue($result->contains($user1));
        $this->assertTrue($result->contains($user2));
    }
}
