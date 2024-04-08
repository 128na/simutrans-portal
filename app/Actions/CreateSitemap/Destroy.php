<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

use Illuminate\Filesystem\FilesystemAdapter;

final class Destroy
{
    public function __construct(private readonly FilesystemAdapter $filesystem)
    {
    }

    public function __invoke(): void
    {
        $files = $this->filesystem->allFiles('/');

        foreach ($files as $file) {
            if (str_ends_with($file, '.xml')) {
                $this->filesystem->delete($file);
            }
        }
    }
}
