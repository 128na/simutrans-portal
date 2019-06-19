<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

class UserLoggedin
{
    use SerializesModels;

    public $user;

    /**
     * 新しいイベントインスタンスの生成
     *
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
