<?php
namespace App\Listeners;

use App\Events\UserLoggedin;
use App\Mail\LoginNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class SendLoginNotification
{
    /**
     * イベントリスナ生成
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * イベントの処理
     *
     * @param  \App\Events\UserLoggedin  $event
     * @return void
     */
    public function handle(UserLoggedin $event)
    {
        $user = Auth::user();
        Mail::to($user->email)->send(new LoginNotification($user));
    }
}
