<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
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
    }

    protected function tearDown(): void
    {
        $disk = Storage::disk('public');
        User::with('myAttachments')->get()->map(static function (User $user) use ($disk) : void {
            $user->myAttachments
                ->filter(static fn(Attachment $attachment) => Str::startsWith($attachment->path, 'user/'))
                ->map(static fn(Attachment $attachment) => $attachment->delete());
            $dir = 'user/' . $user->id;
            if (count($disk->files($dir)) === 0) {
                $disk->deleteDirectory($dir);
            }
        });
        parent::tearDown();
    }

    public static function dataStatus(): \Generator
    {
        yield '公開' => ['publish', true];
        yield '下書き' => ['draft', false];
        yield '非公開' => ['private', false];
        yield 'ゴミ箱' => ['trash', false];
    }

    public static function dataStatusPrivate(): \Generator
    {
        yield '下書き' => ['draft'];
        yield '非公開' => ['private'];
        yield 'ゴミ箱' => ['trash'];
    }
}
