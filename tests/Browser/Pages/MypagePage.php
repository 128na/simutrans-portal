<?php

declare(strict_types=1);

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class MypagePage extends Page
{
    public function url()
    {
        return '/mypage';
    }

    public function assert(Browser $browser): void
    {
        $browser->waitForText('ログイン');
        $browser->assertSee('ログイン');
    }
}
