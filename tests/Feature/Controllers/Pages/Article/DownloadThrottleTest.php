<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages\Article;

use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\TestCase;

/**
 * articles.download のレート制限と CV（1 リクエスト 1 回）の検証
 */
class DownloadThrottleTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_ダウンロード1回につきcvは1回だけ増える(): void
    {
        $article = $this->createDownloadableArticle('first.zip');
        $other = $this->createDownloadableArticle('second.zip');

        $this->get(route('articles.download', ['article' => $article]))->assertOk();
        $this->get(route('articles.download', ['article' => $article]))->assertOk();
        $this->get(route('articles.download', ['article' => $other]))->assertOk();

        $this->assertSame(2, $this->totalConversion($article));
        $this->assertSame(1, $this->totalConversion($other));
    }

    public function test_headリクエストはcvに数えない(): void
    {
        $article = $this->createDownloadableArticle('head.zip');

        $this->call('HEAD', route('articles.download', ['article' => $article]))->assertOk();

        $this->assertSame(0, $this->totalConversion($article));
    }

    public function test_実ファイルが無いときは404でcvに数えない(): void
    {
        $article = $this->createDownloadableArticle('missing.zip');
        Storage::disk('public')->delete('user/missing.zip');

        $this->get(route('articles.download', ['article' => $article]))->assertNotFound();

        $this->assertSame(0, $this->totalConversion($article));
    }

    public function test_作者本人のダウンロードはcvに数えない(): void
    {
        $article = $this->createDownloadableArticle('own.zip');

        $this->actingAs($article->user)
            ->get(route('articles.download', ['article' => $article]))
            ->assertOk();

        $this->assertSame(0, $this->totalConversion($article));
    }

    public function test_1分の上限を超えると429とretry_afterが返りcvは増えない(): void
    {
        $article = $this->createDownloadableArticle('limit.zip');

        for ($i = 0; $i < RouteServiceProvider::DOWNLOAD_PER_MINUTE; $i++) {
            $this->get(route('articles.download', ['article' => $article]))->assertOk();
        }

        $testResponse = $this->get(route('articles.download', ['article' => $article]));

        $testResponse->assertStatus(429);
        $testResponse->assertHeader('Retry-After');
        $this->assertGreaterThan(0, (int) $testResponse->headers->get('Retry-After'));
        $this->assertSame(RouteServiceProvider::DOWNLOAD_PER_MINUTE, $this->totalConversion($article));
    }

    public function test_1時間の上限は1分の上限と別に数える(): void
    {
        $article = $this->createDownloadableArticle('hour.zip');
        $perMinute = RouteServiceProvider::DOWNLOAD_PER_MINUTE;
        $minutesToReachHourLimit = intdiv(RouteServiceProvider::DOWNLOAD_PER_HOUR, $perMinute);

        for ($minute = 0; $minute < $minutesToReachHourLimit; $minute++) {
            $this->travel(61)->seconds();
            for ($i = 0; $i < $perMinute; $i++) {
                $this->get(route('articles.download', ['article' => $article]))->assertOk();
            }
        }

        // 1 分の枠は空いているが、1 時間の枠を使い切っている
        $this->travel(61)->seconds();
        $this->get(route('articles.download', ['article' => $article]))->assertStatus(429);
        $this->assertSame(RouteServiceProvider::DOWNLOAD_PER_HOUR, $this->totalConversion($article));
    }

    public function test_未ログインはipごとに上限を数える(): void
    {
        $article = $this->createDownloadableArticle('ip.zip');

        for ($i = 0; $i < RouteServiceProvider::DOWNLOAD_PER_MINUTE; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
                ->get(route('articles.download', ['article' => $article]))
                ->assertOk();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.10'])
            ->get(route('articles.download', ['article' => $article]))
            ->assertStatus(429);
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.20'])
            ->get(route('articles.download', ['article' => $article]))
            ->assertOk();
    }

    public function test_ログイン中は同じipでもユーザーごとに上限を数える(): void
    {
        $article = $this->createDownloadableArticle('user.zip');
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        for ($i = 0; $i < RouteServiceProvider::DOWNLOAD_PER_MINUTE; $i++) {
            $this->actingAs($userA)
                ->get(route('articles.download', ['article' => $article]))
                ->assertOk();
        }

        $this->actingAs($userA)
            ->get(route('articles.download', ['article' => $article]))
            ->assertStatus(429);
        $this->actingAs($userB)
            ->get(route('articles.download', ['article' => $article]))
            ->assertOk();
    }

    public function test_記事詳細やコンバージョン経路にはダウンロードの上限をかけない(): void
    {
        $article = Article::factory()->publish()->addonIntroduction()->create();

        for ($i = 0; $i <= RouteServiceProvider::DOWNLOAD_PER_MINUTE; $i++) {
            $this->get(route('articles.conversion', ['article' => $article]))->assertRedirect();
        }
    }

    private function createDownloadableArticle(string $fileName): Article
    {
        $author = User::factory()->create();
        $path = 'user/'.$fileName;
        Storage::disk('public')->put($path, 'dummy-'.$fileName);

        $attachment = Attachment::factory()->create([
            'user_id' => $author->id,
            'original_name' => $fileName,
            'path' => $path,
        ]);
        $article = Article::factory()->publish()->addonPost($attachment)->create(['user_id' => $author->id]);
        $attachment->update(['attachmentable_id' => $article->id, 'attachmentable_type' => Article::class]);

        return $article->refresh();
    }

    private function totalConversion(Article $article): int
    {
        return (int) DB::table('conversion_counts')
            ->where('article_id', $article->id)
            ->where('period', 'total')
            ->value('count');
    }
}
