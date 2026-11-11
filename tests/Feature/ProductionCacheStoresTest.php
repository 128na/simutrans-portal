<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\Backup\SyncUploadsDigestTracker;
use App\Actions\Backup\UploadBackupHealth;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Cache\DatabaseStore;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider as TwoFactorAuthenticationProviderContract;
use PragmaRX\Google2FA\Google2FA;
use ReflectionProperty;

/**
 * 本番で予定している構成（既定キャッシュ = file、レート制限 = database）で、
 * 各機能が想定したストアに書き込むことを確かめる。
 * phpunit.xml は CACHE_DRIVER=array 固定のため、この構成は他のテストでは動かない。
 */
class ProductionCacheStoresTest extends TestCase
{
    private string $cachePath;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->cachePath = storage_path('framework/testing/production-cache-'.Str::uuid());
        $this->useProductionCacheStores();
    }

    #[\Override]
    protected function tearDown(): void
    {
        try {
            File::deleteDirectory($this->cachePath);
        } finally {
            parent::tearDown();
        }
    }

    public function test_設定を切り替えた後は既定がfile_レート制限がdatabaseのストアになっている(): void
    {
        $this->assertInstanceOf(FileStore::class, Cache::store()->getStore());
        $this->assertInstanceOf(DatabaseStore::class, $this->rateLimiterStore());
    }

    public function test_webのレート制限はdatabaseに記録されfileには書かれず上限を超えると429になる(): void
    {
        $article = $this->createDownloadableArticle('limit.zip');
        $before = $this->databaseCacheCount();

        for ($i = 0; $i < RouteServiceProvider::DOWNLOAD_PER_MINUTE; $i++) {
            $this->get(route('articles.download', ['article' => $article]))->assertOk();
        }

        $this->get(route('articles.download', ['article' => $article]))
            ->assertStatus(429)
            ->assertHeader('Retry-After');
        $this->assertGreaterThan($before, $this->databaseCacheCount());
        $this->assertSame([], $this->filesInCacheDirectory());
    }

    public function test_ログインのレート制限はdatabaseに記録されfileには書かれない(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct-password')]);
        $before = $this->databaseCacheCount();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/auth/login', ['email' => $user->email, 'password' => 'wrong-password-'.$attempt])
                ->assertUnprocessable();
        }

        $this->postJson('/auth/login', ['email' => $user->email, 'password' => 'wrong-password-last'])
            ->assertStatus(429);
        $this->assertGuest();
        $this->assertGreaterThan($before, $this->databaseCacheCount());
        $this->assertSame([], $this->filesInCacheDirectory());
    }

    public function test_スケジュールの停止状態はfileに保存されdatabaseには書かれず再開で解除される(): void
    {
        $before = $this->databaseCacheCount();

        $this->assertSame(0, Artisan::call('schedule:pause'));

        $this->assertTrue(Cache::store('file')->has('illuminate:schedule:paused'));
        $this->assertNotSame([], $this->filesInCacheDirectory());

        $this->assertSame(0, Artisan::call('schedule:resume'));

        $this->assertFalse(Cache::store('file')->has('illuminate:schedule:paused'));
        $this->assertSame($before, $this->databaseCacheCount());
    }

    public function test_アップロードバックアップの記録はfileに保存され読み戻せる(): void
    {
        $this->travelTo(now()->setDate(2026, 3, 14)->setTime(9, 30));
        $before = $this->databaseCacheCount();
        $health = $this->app->make(UploadBackupHealth::class);
        $digest = $this->app->make(SyncUploadsDigestTracker::class);

        $health->recordSuccess('dropbox');
        $digest->recordTransferred(7);
        $digest->recordTransferred(5);
        $digest->recordError();
        $digest->recordError();
        $digest->recordError();

        $this->assertSame(now()->getTimestamp(), Cache::store('file')->get('backup-sync-uploads-health:dropbox:last-complete'));
        $this->assertNull(Cache::store('file')->get('backup-sync-uploads-health:local_backup:last-complete'));
        $this->assertSame(12, $digest->transferredOn('2026-03-14'));
        $this->assertSame(3, $digest->errorsOn('2026-03-14'));
        $this->assertSame(0, $digest->transferredOn('2026-03-13'));
        // increment しても最初に付けた2日の期限が保たれる
        $this->assertSame(
            now()->addDays(2)->getTimestamp(),
            $this->expirationOf('backup-sync-uploads-digest:transferred:2026-03-14'),
        );
        $this->assertSame($before, $this->databaseCacheCount());
    }

    public function test_二要素認証の使用済みコードはfileに記録され同じコードの再利用は拒否される(): void
    {
        $before = $this->databaseCacheCount();
        $google2fa = $this->app->make(Google2FA::class);
        $secret = $google2fa->generateSecretKey();
        $code = $google2fa->getCurrentOtp($secret);
        $provider = $this->app->make(TwoFactorAuthenticationProviderContract::class);

        $this->assertTrue($provider->verify($secret, $code));
        $this->assertTrue(Cache::store('file')->has('fortify.2fa_codes.'.md5($code)));
        $this->assertFalse($provider->verify($secret, $code));
        $this->assertSame($before, $this->databaseCacheCount());
    }

    /**
     * 既定キャッシュ・レート制限のストアを本番の構成にし、解決済みのシングルトンを作り直す。
     * RateLimiter は起動時にキャッシュを掴んで名前付きリミッターも登録済みなので、
     * 登録済みのリミッター定義を新しいインスタンスへ引き継ぐ。
     */
    private function useProductionCacheStores(): void
    {
        $limiters = new ReflectionProperty(RateLimiter::class, 'limiters');
        $registered = $limiters->getValue($this->app->make(RateLimiter::class));

        config([
            'cache.default' => 'file',
            'cache.stores.file.path' => $this->cachePath,
            'cache.limiter' => 'database',
        ]);
        foreach (['cache', 'cache.store', 'cache.psr6', RateLimiter::class, TwoFactorAuthenticationProviderContract::class] as $abstract) {
            $this->app->forgetInstance($abstract);
        }
        Cache::clearResolvedInstances();
        \Illuminate\Support\Facades\RateLimiter::clearResolvedInstances();

        $limiters->setValue($this->app->make(RateLimiter::class), $registered);
    }

    private function rateLimiterStore(): mixed
    {
        $cache = new ReflectionProperty(RateLimiter::class, 'cache');

        return $cache->getValue($this->app->make(RateLimiter::class))->getStore();
    }

    private function databaseCacheCount(): int
    {
        return DB::table('cache')->count();
    }

    /** @return list<string> */
    private function filesInCacheDirectory(): array
    {
        if (! File::isDirectory($this->cachePath)) {
            return [];
        }

        return array_values(array_map(
            fn (\SplFileInfo $file): string => $file->getPathname(),
            File::allFiles($this->cachePath),
        ));
    }

    private function expirationOf(string $key): int
    {
        $hash = sha1($key);
        $path = $this->cachePath.'/'.substr($hash, 0, 2).'/'.substr($hash, 2, 2).'/'.$hash;

        return (int) substr(File::get($path), 0, 10);
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
}
