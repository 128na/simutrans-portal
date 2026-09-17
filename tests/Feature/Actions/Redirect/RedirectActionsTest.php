<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Redirect;

use App\Actions\Article\Data\UpdateArticleData;
use App\Actions\Article\UpdateArticle;
use App\Actions\Redirect\AddRedirect;
use App\Actions\Redirect\DeleteRedirect;
use App\Actions\Redirect\DoRedirectIfExists;
use App\Actions\Redirect\FindMyRedirects;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Redirect;
use App\Models\User;
use App\Repositories\RedirectRepository;
use Mockery\MockInterface;
use Tests\Feature\TestCase;

class RedirectActionsTest extends TestCase
{
    public function test_add_redirect_calls_repository_store(): void
    {
        $user = User::factory()->create(['nickname' => uniqid('alice_')]);
        $old = 'old-slug';
        $new = 'new-slug';

        $this->mock(RedirectRepository::class, function (MockInterface $mock) use ($user, $old, $new): void {
            $expectedFromSuffix = '/users/'.$user->nickname.'/'.$old;
            $expectedToSuffix = '/users/'.$user->nickname.'/'.$new;

            $mock->shouldReceive('store')->once()->withArgs(function (array $arg) use ($user, $expectedFromSuffix, $expectedToSuffix): bool {
                return isset($arg['user_id'])
                    && $arg['user_id'] === $user->id
                    && isset($arg['from'])
                    && str_ends_with($arg['from'], $expectedFromSuffix)
                    && isset($arg['to'])
                    && str_ends_with($arg['to'], $expectedToSuffix);
            });
        });

        // Ensure app.url is set so route() returns full URL (it will be stripped in AddRedirect)
        config(['app.url' => 'http://localhost']);

        $sut = app(AddRedirect::class);
        ($sut)($user, $old, $new);

        // satisfy PHPUnit that this test performed an assertion (mock expectations cover behavior)
        $this->assertTrue(true);
    }

    public function test_add_redirect_urlは日本語スラッグを二重エンコードしない(): void
    {
        $user = User::factory()->create(['nickname' => uniqid('yuki_')]);
        // 更新前・更新後どちらも、保存済み記事の slug カラムを模した urlencode() 済みの値
        // （Slugable::setSlugAttribute参照）。UpdateArticleは両方ともArticle::slug属性から渡す。
        $oldSlug = urlencode('旧スラッグ');
        $newSlug = urlencode('新スラッグ');

        $this->mock(RedirectRepository::class, function (MockInterface $mock) use ($user): void {
            $mock->shouldReceive('store')->once()->withArgs(function (array $arg) use ($user): bool {
                if (! isset($arg['user_id'], $arg['from'], $arg['to']) || $arg['user_id'] !== $user->id) {
                    return false;
                }

                // 二重エンコードの痕跡（%25）が含まれないこと
                if (str_contains((string) $arg['from'], '%25') || str_contains((string) $arg['to'], '%25')) {
                    return false;
                }

                $fromSlug = urldecode((string) basename((string) $arg['from']));
                $toSlug = urldecode((string) basename((string) $arg['to']));

                return $fromSlug === '旧スラッグ' && $toSlug === '新スラッグ';
            });
        });

        config(['app.url' => 'http://localhost']);

        $sut = app(AddRedirect::class);
        ($sut)($user, $oldSlug, $newSlug);

        $this->assertTrue(true);
    }

    public function test_update_article_with_non_ascii_slug_rename_stores_a_singly_encoded_redirect(): void
    {
        // UpdateArticle→AddRedirectの実結線を、実DB保存を通して検証する回帰テスト。
        // $newSlugはUpdateArticleData(リクエスト入力由来の生値)ではなく、
        // articleRepository->update()保存後のArticle::slug(urlencode済み)から渡される必要がある。
        $user = User::factory()->create(['nickname' => uniqid('taro_')]);
        $article = Article::factory()->publish()->addonIntroduction()->create([
            'user_id' => $user->id,
            'title' => '旧タイトル',
            'slug' => '旧スラッグ',
        ]);

        $data = UpdateArticleData::fromArray([
            'article' => [
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'title' => '新タイトル',
                'slug' => '新スラッグ',
                'status' => ArticleStatus::Publish->value,
                'contents' => [
                    'description' => 'dummy',
                    'license' => 'dummy',
                    'thanks' => 'dummy',
                    'author' => 'dummy',
                    'link' => 'https://example.com',
                    'agreement' => true,
                    'exclude_link_check' => false,
                ],
                'articles' => [],
            ],
            'follow_redirect' => true,
            'without_update_modified_at' => false,
        ]);

        app(UpdateArticle::class)($article, $data);

        $redirect = Redirect::where('user_id', $user->id)->firstOrFail();

        $this->assertStringNotContainsString('%25', $redirect->from);
        $this->assertStringNotContainsString('%25', $redirect->to);
        $this->assertStringContainsString(urlencode('旧スラッグ'), $redirect->from);
        $this->assertStringContainsString(urlencode('新スラッグ'), $redirect->to);
    }

    public function test_delete_redirect_deletes_model(): void
    {
        $redirect = Redirect::factory()->create();

        $sut = new DeleteRedirect;
        ($sut)($redirect);

        $this->assertNull(Redirect::find($redirect->id));
    }

    public function test_do_redirect_if_exists_returns_permanent_redirect(): void
    {
        config(['app.url' => 'http://localhost']);

        $user = User::factory()->create(['nickname' => uniqid('bob_')]);
        $from = '/users/'.$user->nickname.'/old';
        $to = '/users/'.$user->nickname.'/new';

        $redirect = Redirect::factory()->create(['user_id' => $user->id, 'from' => $from, 'to' => $to]);

        $sut = app(DoRedirectIfExists::class);

        $full = config('app.url').$from;
        $response = ($sut)($full);

        $this->assertSame(301, $response->getStatusCode());
        $location = $response->headers->get('Location');
        $this->assertIsString($location);
        $this->assertStringEndsWith($to, $location);
    }

    public function test_find_my_redirects_returns_user_redirects(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Redirect::factory()->create(['user_id' => $user->id]);
        Redirect::factory()->create(['user_id' => $user->id]);
        Redirect::factory()->create(['user_id' => $other->id]);

        $sut = app(FindMyRedirects::class);
        $collection = ($sut)($user);

        $this->assertCount(2, $collection);
        $this->assertContainsOnlyInstancesOf(Redirect::class, $collection->all());
    }
}
