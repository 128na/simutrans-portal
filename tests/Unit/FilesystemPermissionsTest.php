<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnixVisibility\VisibilityConverter;
use ReflectionProperty;

class FilesystemPermissionsTest extends TestCase
{
    public function test_uploads_and_visibility_changes_do_not_restore_group_write_permissions(): void
    {
        $root = storage_path('framework/testing/permissions-'.bin2hex(random_bytes(8)));
        $disk = Storage::build(array_replace(config('filesystems.disks.public'), ['root' => $root]));

        try {
            $converter = (new ReflectionProperty($disk->getAdapter(), 'visibility'))->getValue($disk->getAdapter());
            $this->assertInstanceOf(VisibilityConverter::class, $converter);
            $this->assertSame(0644, $converter->forFile('public'));
            $this->assertSame(0600, $converter->forFile('private'));
            $this->assertSame(0755, $converter->forDirectory('public'));
            $this->assertSame(0700, $converter->forDirectory('private'));
            $this->assertSame(0755, $converter->defaultForDirectories());

            $this->assertTrue($disk->put('uploads/example.txt', 'uploaded content'));
            $this->assertSame('uploaded content', $disk->get('uploads/example.txt'));
            $this->assertTrue($disk->put('private/example.txt', 'private content', [
                'visibility' => 'private',
                'directory_visibility' => 'private',
            ]));

            if (PHP_OS_FAMILY !== 'Windows') {
                $this->assertPermissions(0644, $disk->path('uploads/example.txt'));
                $this->assertPermissions(0755, $disk->path('uploads'));
                $this->assertPermissions(0600, $disk->path('private/example.txt'));
                $this->assertPermissions(0700, $disk->path('private'));
            }

            $this->assertTrue($disk->setVisibility('uploads/example.txt', 'private'));
            if (PHP_OS_FAMILY !== 'Windows') {
                $this->assertPermissions(0600, $disk->path('uploads/example.txt'));
            }
            $this->assertTrue($disk->setVisibility('uploads/example.txt', 'public'));
            if (PHP_OS_FAMILY !== 'Windows') {
                $this->assertPermissions(0644, $disk->path('uploads/example.txt'));
            }
        } finally {
            File::deleteDirectory($root);
        }
    }

    private function assertPermissions(int $expected, string $path): void
    {
        clearstatcache(true, $path);
        $this->assertSame($expected, fileperms($path) & 0777);
    }
}
