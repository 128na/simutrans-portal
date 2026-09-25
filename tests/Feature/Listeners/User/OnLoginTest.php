<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners\User;

use App\Models\User;
use App\Notifications\SendLoggedInEmail;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

/**
 * OnLogin リスナーが実際のログイン経路（AuthenticatedSessionController）を通したときに
 * 保証すべき振る舞いを検証する。
 *
 * isNewLogin() はバックトレースに AuthenticatedSessionController /
 * TwoFactorAuthenticatedSessionController が含まれるかで新規ログインかどうかを判定しており、
 * Unit テストでは常に false になってしまうため（tests/Unit/Listeners/User/OnLoginTest.php 参照）、
 * 本体（isNewLogin のバックトレース依存）を変えずに、実際に POST /auth/login を叩く
 * Feature テストとして検証する。
 */
class OnLoginTest extends TestCase
{
    public function test_creates_login_history_and_sends_notification_and_logs_audit_on_new_login(): void
    {
        Notification::fake();
        Log::shouldReceive('channel')->once()->with('audit')->andReturnSelf();

        $user = User::factory()->create(['password' => bcrypt('password')]);

        Log::shouldReceive('info')->once()->with('ログイン', [
            'userId' => $user->id,
            'userName' => $user->name,
        ]);

        $this->assertDatabaseCount('login_histories', 0);

        $testResponse = $this->postJson('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $testResponse->assertOk();
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('login_histories', [
            'user_id' => $user->id,
        ]);

        Notification::assertSentTo(
            $user,
            SendLoggedInEmail::class,
            function (SendLoggedInEmail $notification) use ($user): bool {
                return $notification->loginHistory->user_id === $user->id;
            }
        );
    }

    public function test_does_not_create_login_history_or_notify_when_login_event_is_not_dispatched_via_controller(): void
    {
        // AuthenticatedSessionController / TwoFactorAuthenticatedSessionController を経由しない
        // Login イベント発火（例: 別経路でのプログラム的な Auth::login）では、
        // isNewLogin() が false を返し、通知・ログイン履歴作成・監査ログ出力が行われないことを確認する。
        // このテスト自身のスタックには対象コントローラが含まれないため、実際のバックトレース判定を
        // 本体のコードのまま検証できる。
        Notification::fake();
        Log::shouldReceive('channel')->never();

        $user = User::factory()->create();

        Auth::login($user);
        event(new Login('web', $user, false));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('login_histories', 0);
        Notification::assertNothingSent();
    }

    public function test_does_not_process_when_user_is_not_authenticatable_user_instance(): void
    {
        // Unit/Listeners/User/OnLoginTest.php で検証済みの
        // 「User インスタンスでなければ何もしない」分岐は、Feature 側でも
        // ログイン履歴が作られないことで間接的に確認する。
        Log::shouldReceive('channel')->never();

        $this->assertDatabaseCount('login_histories', 0);

        event(new Login('web', new \stdClass, false));

        $this->assertDatabaseCount('login_histories', 0);
    }
}
