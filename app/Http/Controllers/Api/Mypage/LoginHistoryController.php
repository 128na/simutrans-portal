<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;

final class LoginHistoryController extends Controller
{
    /**
     * @return Collection<int,\App\Models\User\LoginHistory>
     */
    public function index(): Collection
    {
        return $this->loggedinUser()
            ->loginHistories()
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }
}
