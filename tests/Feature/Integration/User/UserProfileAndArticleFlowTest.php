<?php

declare(strict_types=1);

namespace Tests\Feature\Integration\User;

use App\Actions\Article\StoreArticle;
use App\Actions\User\UpdateProfile;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Http\Requests\User\UpdateRequest;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\TestCase;

/**
 * ユーザープロフィール・記事投稿統合テスト
 * プロフィール設定 → 記事投稿 → アバター設定の一連の流れを検証
 */
class UserProfileAndArticleFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * プロフィール更新後に記事投稿が正常に動作する
     */
    public function test_profile_update_then_article_creation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // プロフィール更新リクエストを作成
        $updateRequest = $this->createUpdateRequest([
            'user' => [
                'name' => 'Updated Name',
                'nickname' => 'updated_nick',
                'email' => $user->email,
                'profile' => [
                    'data' => [
                        'description' => 'Updated description',
                        'website' => ['https://example.com'],
                        'avatar' => null,
                    ],
                ],
            ],
        ]);

        // プロフィール更新
        $updateProfileAction = app(UpdateProfile::class);
        $updatedUser = $updateProfileAction($user, $updateRequest);

        // プロフィールが更新されたことを確認
        $this->assertEquals('Updated Name', $updatedUser->name);
        $this->assertEquals('updated_nick', $updatedUser->nickname);
        $this->assertEquals('Updated description', $updatedUser->profile->data->description);

        // 記事投稿
        $articleData = [
            'should_notify' => false,
            'article' => [
                'status' => ArticleStatus::Publish->value,
                'title' => 'My First Article',
                'slug' => 'my-first-article',
                'post_type' => ArticlePostType::Markdown->value,
                'contents' => ['markdown' => '# Hello World'],
            ],
        ];

        $storeArticleAction = app(StoreArticle::class);
        $article = $storeArticleAction($updatedUser, $articleData);

        // 記事が作成されたことを確認
        $this->assertInstanceOf(\App\Models\Article::class, $article);
        $this->assertEquals($updatedUser->id, $article->user_id);
        $this->assertEquals('My First Article', $article->title);

        // 記事がユーザーに紐づいていることを確認
        $this->assertTrue($updatedUser->articles->contains($article));
    }

    /**
     * @test
     * アバター設定とプロフィール更新が正常に動作する
     */
    public function test_avatar_setting_and_profile_update(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // アバター用の添付ファイル作成
        $avatar = Attachment::factory()->create([
            'user_id' => $user->id,
            'path' => 'avatars/test-avatar.jpg',
        ]);

        // アバター付きプロフィール更新
        $updateRequest = $this->createUpdateRequest([
            'user' => [
                'name' => $user->name,
                'nickname' => $user->nickname,
                'email' => $user->email,
                'profile' => [
                    'data' => [
                        'avatar' => $avatar->id,
                        'description' => 'Profile with avatar',
                        'website' => [],
                    ],
                ],
            ],
        ]);

        $updateProfileAction = app(UpdateProfile::class);
        $updatedUser = $updateProfileAction($user, $updateRequest);

        // アバターが設定されたことを確認
        $this->assertEquals($avatar->id, $updatedUser->profile->data->avatar);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $updatedUser->id,
        ]);

        // 添付ファイルがユーザープロフィールに関連付けられていることを確認
        $this->assertDatabaseHas('attachments', [
            'id' => $avatar->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     * メールアドレス変更時に認証状態がリセットされる
     */
    public function test_email_change_resets_verification(): void
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        // メールアドレス変更
        $updateRequest = $this->createUpdateRequest([
            'user' => [
                'name' => $user->name,
                'nickname' => $user->nickname,
                'email' => 'new@example.com',
                'profile' => [
                    'data' => [
                        'avatar' => null,
                        'description' => '',
                        'website' => [],
                    ],
                ],
            ],
        ]);

        $updateProfileAction = app(UpdateProfile::class);
        $updatedUser = $updateProfileAction($user, $updateRequest);

        // メールアドレスが変更されたことを確認
        $this->assertEquals('new@example.com', $updatedUser->email);

        // 認証状態がリセットされたことを確認
        $this->assertNull($updatedUser->email_verified_at);

        // DBに保存されていることを確認
        $this->assertDatabaseHas('users', [
            'id' => $updatedUser->id,
            'email' => 'new@example.com',
            'email_verified_at' => null,
        ]);
    }

    /**
     * @test
     * ウェブサイトURLのフィルタリングが正常に動作する
     */
    public function test_website_url_filtering(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 空文字やnullを含むウェブサイトリスト
        $updateRequest = $this->createUpdateRequest([
            'user' => [
                'name' => $user->name,
                'nickname' => $user->nickname,
                'email' => $user->email,
                'profile' => [
                    'data' => [
                        'avatar' => null,
                        'description' => '',
                        'website' => [
                            'https://example.com',
                            '', // 空文字
                            'https://github.com/user',
                            null, // null
                            'https://twitter.com/user',
                        ],
                    ],
                ],
            ],
        ]);

        $updateProfileAction = app(UpdateProfile::class);
        $updatedUser = $updateProfileAction($user, $updateRequest);

        // 空文字とnullがフィルタリングされていることを確認
        $websites = $updatedUser->profile->data->website;
        $this->assertCount(3, $websites);
        $this->assertContains('https://example.com', $websites);
        $this->assertContains('https://github.com/user', $websites);
        $this->assertContains('https://twitter.com/user', $websites);
        $this->assertNotContains('', $websites);
        $this->assertNotContains(null, $websites);
    }

    /**
     * @test
     * 複数記事投稿後もプロフィール情報が保持される
     */
    public function test_multiple_articles_with_profile_integrity(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // プロフィール設定
        $updateRequest = $this->createUpdateRequest([
            'user' => [
                'name' => 'Article Author',
                'nickname' => 'author123',
                'email' => $user->email,
                'profile' => [
                    'data' => [
                        'avatar' => null,
                        'description' => 'Prolific writer',
                        'website' => ['https://blog.example.com'],
                    ],
                ],
            ],
        ]);

        $updateProfileAction = app(UpdateProfile::class);
        $updatedUser = $updateProfileAction($user, $updateRequest);

        // 複数記事を投稿
        $storeArticleAction = app(StoreArticle::class);
        $articles = [];

        for ($i = 1; $i <= 3; $i++) {
            $articles[] = $storeArticleAction($updatedUser, [
                'should_notify' => false,
                'article' => [
                    'status' => ArticleStatus::Publish->value,
                    'title' => "Article {$i}",
                    'slug' => "article-{$i}",
                    'post_type' => ArticlePostType::Page->value,
                    'contents' => ['sections' => []],
                ],
            ]);
        }

        // 記事が3つ作成されたことを確認
        $this->assertCount(3, $articles);

        // プロフィール情報が保持されていることを確認
        $updatedUser->refresh();
        $this->assertEquals('Article Author', $updatedUser->name);
        $this->assertEquals('author123', $updatedUser->nickname);
        $this->assertEquals('Prolific writer', $updatedUser->profile->data->description);

        // 全記事がユーザーに紐づいていることを確認
        $this->assertEquals(3, $updatedUser->articles()->count());
    }

    protected function createUpdateRequest(array $data): UpdateRequest
    {
        $request = UpdateRequest::create('/', 'POST', $data);
        $request->setUserResolver(fn() => auth()->user());

        return $request;
    }
}
