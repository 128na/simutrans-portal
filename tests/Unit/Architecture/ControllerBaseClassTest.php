<?php

declare(strict_types=1);

namespace Tests\Unit\Architecture;

use App\Http\Controllers\Controller;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Tests\Unit\TestCase;

class ControllerBaseClassTest extends TestCase
{
    /**
     * Illuminate\Routing\Controllerを直接extendすると、$this->authorize()や
     * loggedinUser()が使えないまま実装され、後から認可チェックを足す際に
     * 基底クラスの変え忘れという同種のバグを繰り返す(実際にMypage配下13
     * コントローラで発生した)。全コントローラがApp\Http\Controllers\Controller
     * を継承していることを固定し、再発を防ぐ。
     */
    public function test_全てのコントローラはアプリ共通の基底クラスを継承している(): void
    {
        $controllersPath = app_path('Http/Controllers');
        $finder = (new Finder)->files()->in($controllersPath)->name('*.php');

        $violations = [];

        foreach ($finder as $file) {
            $class = $this->resolveClassName($file->getRealPath());

            if ($class === Controller::class) {
                continue;
            }

            if (! class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);

            if ($reflection->isInterface() || $reflection->isTrait() || $reflection->isEnum()) {
                continue;
            }

            if (! $reflection->isSubclassOf(Controller::class)) {
                $violations[] = $class;
            }
        }

        $this->assertSame([], $violations, 'The following controllers do not extend '.Controller::class.": \n".implode("\n", $violations));
    }

    private function resolveClassName(string $absolutePath): string
    {
        $relativePath = str_replace(app_path().DIRECTORY_SEPARATOR, '', $absolutePath);
        $withoutExtension = substr($relativePath, 0, -4);

        return 'App\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $withoutExtension);
    }
}
