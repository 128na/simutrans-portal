<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

class FirstOrFailByIdOrNicknameTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository(new User());
    }

    /**
     * @test
     * 数値IDでユーザーを取得できる
     */
    public function testFindsUserById(): void
    {
        $user = User::factory()->create(['id' => 123]);

        $result = $this->repository->firstOrFailByIdOrNickname('123');

        $this->assertEquals($user->id, $result->id);
    }

    /**
     * @test
     * ニックネームでユーザーを取得できる
     */
    public function testFindsUserByNickname(): void
    {
        $user = User::factory()->create(['nickname' => 'test_user']);

        $result = $this->repository->firstOrFailByIdOrNickname('test_user');

        $this->assertEquals($user->nickname, $result->nickname);
    }

    /**
     * @test
     * 存在しないIDでは例外が発生する
     */
    public function testThrowsExceptionForNonExistentId(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->firstOrFailByIdOrNickname('999999');
    }

    /**
     * @test
     * 存在しないニックネームでは例外が発生する
     */
    public function testThrowsExceptionForNonExistentNickname(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->firstOrFailByIdOrNickname('nonexistent_user');
    }

    /**
     * @test
     * 数値形式のニックネームがある場合でもID優先で検索する
     */
    public function testPrioritizesIdOverNickname(): void
    {
        $userWithNumericNickname = User::factory()->create([
            'id' => 100,
            'nickname' => '200',
        ]);

        $userWithId200 = User::factory()->create([
            'id' => 200,
            'nickname' => 'user200',
        ]);

        // '200' は数値として扱われ、ID=200 のユーザーを取得
        $result = $this->repository->firstOrFailByIdOrNickname('200');

        $this->assertEquals(200, $result->id);
        $this->assertEquals($userWithId200->id, $result->id);
        $this->assertNotEquals($userWithNumericNickname->id, $result->id);
    }
}
