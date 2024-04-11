<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

final class MypagePage extends Page
{
    #[\Override]
    public function url()
    {
        return '/mypage';
    }

    #[\Override]
    public function assert(Browser $browser): void
    {
        $browser->waitForText('ログイン');
        $browser->assertSee('ログイン');
    }
}
