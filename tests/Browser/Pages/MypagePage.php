<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class MypagePage extends Page
{
    public function url()
    {
        return '/mypage';
    }

    public function assert(Browser $browser)
    {
        $browser->waitForText('ログイン');
        $browser->assertSee('ログイン');
    }
}
