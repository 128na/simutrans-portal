<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\LoginHistoryRepository;

use App\Models\User;
use App\Models\User\LoginHistory;
use App\Repositories\LoginHistoryRepository;
use Tests\Feature\TestCase;

class GetByUserTest extends TestCase
{
    private LoginHistoryRepository $loginHistoryRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginHistoryRepository = app(LoginHistoryRepository::class);
    }

    public function test_returns_login_histories_for_user(): void
    {
        $user = User::factory()->create();
        $loginHistory = LoginHistory::factory()->create([
            'user_id' => $user->id,
        ]);

        $results = $this->loginHistoryRepository->getByUser($user->id);

        $this->assertCount(1, $results);
        $first = $results->first();
        $this->assertNotNull($first);
        $this->assertEquals($loginHistory->id, $first->id);
        $this->assertEquals($user->id, $first->user_id);
    }

    public function test_returns_histories_ordered_by_latest_first(): void
    {
        $user = User::factory()->create();

        // Create histories with different timestamps
        $firstHistory = LoginHistory::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
        ]);
        $secondHistory = LoginHistory::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(1),
        ]);
        $thirdHistory = LoginHistory::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        $results = $this->loginHistoryRepository->getByUser($user->id);

        $this->assertCount(3, $results);
        $this->assertNotNull($results[0]);
        $this->assertNotNull($results[1]);
        $this->assertNotNull($results[2]);
        $this->assertEquals($thirdHistory->id, $results[0]->id);
        $this->assertEquals($secondHistory->id, $results[1]->id);
        $this->assertEquals($firstHistory->id, $results[2]->id);
    }

    public function test_limits_results_to_10_records(): void
    {
        $user = User::factory()->create();

        // Create 15 login histories
        for ($i = 0; $i < 15; $i++) {
            LoginHistory::factory()->create([
                'user_id' => $user->id,
                'created_at' => now()->subDays(14 - $i),
            ]);
        }

        $results = $this->loginHistoryRepository->getByUser($user->id);

        $this->assertCount(10, $results);
    }

    public function test_returns_empty_collection_for_user_with_no_history(): void
    {
        $user = User::factory()->create();

        $results = $this->loginHistoryRepository->getByUser($user->id);

        $this->assertCount(0, $results);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
    }

    public function test_does_not_return_other_users_login_histories(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        LoginHistory::factory()->create(['user_id' => $user1->id]);
        LoginHistory::factory()->create(['user_id' => $user2->id]);

        $results = $this->loginHistoryRepository->getByUser($user1->id);

        $this->assertCount(1, $results);
        $first = $results->first();
        $this->assertNotNull($first);
        $this->assertEquals($user1->id, $first->user_id);
    }

    public function test_returns_only_latest_10_when_more_exist(): void
    {
        $user = User::factory()->create();

        // Create 12 histories with specific timestamps
        $histories = [];
        for ($i = 0; $i < 12; $i++) {
            $histories[] = LoginHistory::factory()->create([
                'user_id' => $user->id,
                'created_at' => now()->subDays(11 - $i),
            ]);
        }

        $results = $this->loginHistoryRepository->getByUser($user->id);

        $this->assertCount(10, $results);
        // Verify the latest 10 are returned
        for ($i = 0; $i < 10; $i++) {
            $resultItem = $results[$i];
            $this->assertNotNull($resultItem);
            $this->assertEquals($histories[11 - $i]->id, $resultItem->id);
        }
    }

    public function test_includes_all_expected_fields(): void
    {
        $user = User::factory()->create();
        $loginHistory = LoginHistory::factory()->create([
            'user_id' => $user->id,
        ]);

        $results = $this->loginHistoryRepository->getByUser($user->id);

        $result = $results->first();
        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertNotNull($result->ip);
        $this->assertNotNull($result->ua);
        $this->assertNotNull($result->created_at);
        $this->assertNotNull($result->updated_at);
        // Verify the structure matches the database schema
        $this->assertEquals($loginHistory->id, $result->id);
    }
}
