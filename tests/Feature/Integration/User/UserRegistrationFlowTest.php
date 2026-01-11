<?php

declare(strict_types=1);

namespace Tests\Feature\Integration\User;

use App\Actions\User\Registration;
use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\UserInvited;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

/**
 * ユーザー登録フロー統合テスト
 * ユーザー招待 → 登録 → 通知送信の一連の流れを検証
 */
class UserRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 完全なユーザー招待・登録フローが正常に動作する
     */
    public function test_complete_user_registration_flow(): void
    {
        Notification::fake();

        // 招待者（管理者）を作成
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        // 新規ユーザーデータ
        $data = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
        ];

        // ユーザー登録アクション実行
        $registrationAction = app(Registration::class);
        $newUser = $registrationAction($data, $admin);

        // ユーザーが作成されたことを確認
        $this->assertInstanceOf(User::class, $newUser);
        $this->assertEquals('New User', $newUser->name);
        $this->assertEquals('newuser@example.com', $newUser->email);
        $this->assertEquals(UserRole::User, $newUser->role);
        $this->assertEquals($admin->id, $newUser->invited_by);

        // パスワードがハッシュ化されていることを確認
        $this->assertTrue(Hash::check('password123', $newUser->password));

        // 招待者に通知が送信されたことを確認
        Notification::assertSentTo($admin, UserInvited::class);

        // DBに保存されていることを確認
        $this->assertDatabaseHas('users', [
            'id' => $newUser->id,
            'email' => 'newuser@example.com',
            'invited_by' => $admin->id,
        ]);
    }

    /**
     * @test
     * 複数ユーザーの連続登録が正常に動作する
     */
    public function test_multiple_user_registrations(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $registrationAction = app(Registration::class);

        // 3人のユーザーを登録
        $users = [];
        for ($i = 1; $i <= 3; $i++) {
            $users[] = $registrationAction([
                'name' => "User {$i}",
                'email' => "user{$i}@example.com",
                'password' => 'password123',
            ], $admin);
        }

        // 全員が登録されていることを確認
        $this->assertCount(3, $users);
        foreach ($users as $user) {
            $this->assertEquals(UserRole::User, $user->role);
            $this->assertEquals($admin->id, $user->invited_by);
        }

        // 3回通知が送信されたことを確認
        Notification::assertSentTo($admin, UserInvited::class, 3);
    }

    /**
     * @test
     * ユーザー登録時にプロフィールが自動作成される
     */
    public function test_user_profile_auto_creation(): void
    {
        $admin = User::factory()->create();

        $registrationAction = app(Registration::class);
        $newUser = $registrationAction([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ], $admin);

        // ユーザーがプロフィールを持っていることを確認（モデルイベントで作成）
        $this->assertNotNull($newUser->profile);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $newUser->id,
        ]);
    }

    /**
     * @test
     * 招待者がAdminでなくてもユーザー登録できる
     */
    public function test_non_admin_can_invite_user(): void
    {
        Notification::fake();

        // 通常ユーザーが招待者
        $inviter = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $registrationAction = app(Registration::class);
        $newUser = $registrationAction([
            'name' => 'Invited User',
            'email' => 'invited@example.com',
            'password' => 'password123',
        ], $inviter);

        // ユーザーが正常に作成されることを確認
        $this->assertInstanceOf(User::class, $newUser);
        $this->assertEquals($inviter->id, $newUser->invited_by);

        // 招待者に通知が送信されることを確認
        Notification::assertSentTo($inviter, UserInvited::class);
    }

    /**
     * @test
     * パスワードが正しくハッシュ化される
     */
    public function test_password_is_hashed_correctly(): void
    {
        $admin = User::factory()->create();

        $plainPassword = 'SecurePassword123!@#';

        $registrationAction = app(Registration::class);
        $newUser = $registrationAction([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $plainPassword,
        ], $admin);

        // パスワードが平文で保存されていないことを確認
        $this->assertNotEquals($plainPassword, $newUser->password);

        // パスワードがハッシュ化されていることを確認
        $this->assertTrue(Hash::check($plainPassword, $newUser->password));

        // 誤ったパスワードで検証が失敗することを確認
        $this->assertFalse(Hash::check('WrongPassword', $newUser->password));
    }
}
