<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\DeadLink;

use App\Actions\DeadLink\NormalizeUrl;
use Tests\Unit\TestCase;

class NormalizeUrlTest extends TestCase
{
    public function test_パスに日本語が含まれる場合はエンコードする(): void
    {
        $actual = $this->getSUT()('https://simutranshouse.wixsite.com/simutp/others/景観用透明セット');

        $this->assertSame(
            'https://simutranshouse.wixsite.com/simutp/others/'.rawurlencode('景観用透明セット'),
            $actual
        );
    }

    public function test_既にpercentエンコード済みのパスは二重エンコードしない(): void
    {
        $encoded = rawurlencode('景観用透明セット');

        $actual = $this->getSUT()('https://example.com/'.$encoded);

        $this->assertSame('https://example.com/'.$encoded, $actual);
    }

    public function test_クエリに日本語が含まれる場合はエンコードする(): void
    {
        $actual = $this->getSUT()('https://example.com/index.php?アドオン/建物4');

        $this->assertStringNotContainsString('建物4', $actual);
        $this->assertStringContainsString('example.com/index.php?', $actual);
    }

    public function test_日本語を含まない場合は変更しない(): void
    {
        $actual = $this->getSUT()('https://example.com/path?foo=bar');

        $this->assertSame('https://example.com/path?foo=bar', $actual);
    }

    public function test_等号を含まないクエリに余計な等号を付与しない(): void
    {
        $actual = $this->getSUT()('http://japanese.simutrans.com/index.php?Addon128Japan%2F%C3%CF%CA%FD%BB%E4%C5%B4');

        $this->assertSame(
            'http://japanese.simutrans.com/index.php?Addon128Japan%2F%C3%CF%CA%FD%BB%E4%C5%B4',
            $actual
        );
    }

    public function test_クエリキーのドットを保持する(): void
    {
        $actual = $this->getSUT()('https://example.com/index.php?Addon128.Japan');

        $this->assertSame('https://example.com/index.php?Addon128.Japan', $actual);
    }

    public function test_重複するクエリキーを保持する(): void
    {
        $actual = $this->getSUT()('https://example.com/?a=1&a=2');

        $this->assertSame('https://example.com/?a=1&a=2', $actual);
    }

    public function test_パス中の16進数字風の文字列を壊さない(): void
    {
        $actual = $this->getSUT()('https://example.com/foo%bar.zip');

        $this->assertSame('https://example.com/foo%bar.zip', $actual);
    }

    public function test_パースできない場合はそのまま返す(): void
    {
        $actual = $this->getSUT()('not a url');

        $this->assertSame('not a url', $actual);
    }

    private function getSUT(): NormalizeUrl
    {
        return app(NormalizeUrl::class);
    }
}
