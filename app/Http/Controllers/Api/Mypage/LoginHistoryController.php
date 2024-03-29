<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;

class LoginHistoryController extends Controller
{
    public function index(): Collection
    {
        return $this->loggedinUser()
            ->loginHistories()
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }
}
