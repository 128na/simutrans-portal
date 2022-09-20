<?php

namespace Tests;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    /**
     * 一般ユーザー
     */
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('ProdSeeder');
        $this->user = User::factory()->create();

        $this->withoutMiddleware(SetCacheHeaders::class);
    }

    protected function tearDown(): void
    {
        $disk = Storage::disk('public');
        User::all()->map(function (User $user) use ($disk) {
            $user->myAttachments
                ->filter(fn (Attachment $a) => Str::startsWith($a->path, 'user/'))
                ->map(fn (Attachment $a) => $a->delete());
            $dir = "user/$user->id";
            if (count($disk->files($dir)) === 0) {
                $disk->deleteDirectory($dir);
            }
        });
        parent::tearDown();
    }

    public function dataStatus()
    {
        yield '公開' => ['publish', true];
        yield '下書き' => ['draft', false];
        yield '非公開' => ['private', false];
        yield 'ゴミ箱' => ['trash', false];
    }

    public function dataStatusPrivate()
    {
        yield '下書き' => ['draft'];
        yield '非公開' => ['private'];
        yield 'ゴミ箱' => ['trash'];
    }
}
