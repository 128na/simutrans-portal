<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Article;
use App\Models\Category;
use App\Models\Profile;
use Illuminate\Http\UploadedFile;
class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
    }

    /**
     * ユーザー登録
     * 下記条件で登録できないこと
     *      空のユーザー名
     *      256文字以上のユーザー名
     *      不正なメールアドレス形式
     *      登録済みのメールアドレス
     *      8文字以下のパスワード
     *      確認フィールド不一致
     * 正しい情報でログインできること
     */
    public function testRegister()
    {
        $response = $this->get('register');
        $response->assertOk();

        $date = now()->format('YmdHis');
        $data = [
            'name' => 'example',
            'email' => "test_{$date}@example.com",
            'password' => 'password'.$date,
            'password_confirmation' => 'password'.$date,
        ];

        $response = $this->post('register', array_merge($data, ['name' => '']));
        $response->assertSessionHasErrors(['name']);
        $response = $this->post('register', array_merge($data, ['name' => str_repeat('a', 256)]));
        $response->assertSessionHasErrors(['name']);

        $response = $this->post('register', array_merge($data, ['email' => 'hoge']));
        $response->assertSessionHasErrors(['email']);
        $registrated_user = factory(User::class)->create();
        $response = $this->post('register', array_merge($data, ['email' => $registrated_user->email]));
        $response->assertSessionHasErrors(['email']);

        $response = $this->post('register', array_merge($data, ['password' => 'hoge']));
        $response->assertSessionHasErrors(['password']);
        $response = $this->post('register', array_merge($data, ['password_confirmation' => 'hoge']));
        $response->assertSessionHasErrors(['password']);

        $response = $this->post('register', $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('mypage');
    }


    /**
     * ログイン
     * 間違ったメールアドレスでログインできないこと
     * 間違ったパスワードでログインできないこと
     * 登録済みのメールアドレス・パスワードでログインできること
     * ログイン後マイページへ移動すること
     * ログイン後、リロードしてもマイページが表示されること
     * ログアウト後トップページへ移動すること
     */
    public function testLogin()
    {
        $user = factory(User::class)->create();

        $response = $this->get('login');
        $response->assertOk();

        $response = $this->post('login', ['email' => $user->email.'wrong', 'password' => 'password']);
        $response->assertSessionHasErrors(['email']);

        $response = $this->post('login', ['email' => $user->email, 'password' => 'password_wrong']);
        $response->assertSessionHasErrors(['email']);

        $response = $this->post('login', ['email' => $user->email, 'password' => 'password']);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('mypage');

        $response = $this->get('mypage');
        $response->assertOk();

        $response = $this->post('logout');
        $response->assertRedirect('/');
    }

    /**
     * ログインが必要なページに未ログインでアクセスした場合、ログインページへリダイレクトすること
     * ログインが必要なページにログイン済みでアクセスした場合、ページが表示されること
     */
    public function testNeedLogin()
    {
        $response = $this->get('mypage');
        $response->assertStatus(302);
        $response->assertRedirect('login');

        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertOk();
    }
}
