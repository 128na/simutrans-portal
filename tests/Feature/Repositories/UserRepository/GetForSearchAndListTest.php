<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\Article;
use App\Models\User;
use App\Repositories\UserRepository;
use Tests\Feature\TestCase;

final class GetForSearchAndListTest extends TestCase
{
    private UserRepository $userRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
    }

    public function test_get_for_search(): void
    {
        $user = User::factory()->create();
        Article::factory()->for($user)->create(['status' => 'publish']);

        $results = $this->userRepository->getForSearch();

        $this->assertNotEmpty($results);
        $this->assertSame($user->name, $results->first()->name);
    }

    public function test_get_for_list(): void
    {
        $user = User::factory()->create();
        Article::factory()->for($user)->create(['status' => 'publish']);

        $results = $this->userRepository->getForList();

        $this->assertNotEmpty($results);
        $this->assertNotNull($results->first()->articles_count);
    }
}
