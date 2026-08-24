<?php

declare(strict_types=1);

namespace Tests\Unit\OpenApi;

use Symfony\Component\Finder\Finder;
use Tests\Unit\TestCase;

class SwaggerAnnotationPathsTest extends TestCase
{
    /**
     * config/l5-swagger.php の paths.annotations はディレクトリの手動列挙で
     * 管理されている。実際にMypage配下6コントローラのOpenAPI属性がここに
     * 追加され忘れ、長期間ドキュメント生成対象から漏れていたことがある。
     * OA属性を使うファイルが1つでもスキャン対象外だと検知できるようにし、
     * 同じ形の設定ドリフトの再発を防ぐ。
     */
    public function test_openapi属性を使う全ファイルがl5swaggerのスキャン対象に含まれている(): void
    {
        /** @var list<string> $annotationPaths */
        $annotationPaths = config('l5-swagger.documentations.default.paths.annotations');

        $finder = (new Finder)->files()->in(app_path())->name('*.php')->contains('#[OA\\');

        $uncovered = [];

        foreach ($finder as $file) {
            $path = $file->getRealPath();

            if (! $this->isCovered($path, $annotationPaths)) {
                $uncovered[] = $path;
            }
        }

        $this->assertSame(
            [],
            $uncovered,
            "The following files use OpenAPI attributes but are outside l5-swagger's paths.annotations scan list:\n".implode("\n", $uncovered)
        );
    }

    /**
     * @param  list<string>  $annotationPaths
     */
    private function isCovered(string $filePath, array $annotationPaths): bool
    {
        $normalizedFilePath = $this->normalizeSeparators($filePath);

        foreach ($annotationPaths as $annotationPath) {
            $normalizedAnnotationPath = $this->normalizeSeparators($annotationPath);

            if ($normalizedFilePath === $normalizedAnnotationPath) {
                return true;
            }

            $directory = rtrim($normalizedAnnotationPath, '/').'/';
            if (str_starts_with($normalizedFilePath, $directory)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeSeparators(string $path): string
    {
        return str_replace('\\', '/', $path);
    }
}
