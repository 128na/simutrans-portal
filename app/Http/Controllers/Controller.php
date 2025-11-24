<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Simutrans Portal API',
    description: 'Simutrans Addon Portal API Documentation'
)]
#[OA\Server(
    url: 'http://localhost',
    description: 'Local Development Server'
)]
#[OA\Server(
    url: 'https://portal.128-bit.net',
    description: 'Production Server'
)]
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected function loggedinUser(): User
    {
        $user = Auth::user();
        if ($user instanceof User) {
            return $user;
        }

        throw new Exception('user not found');
    }
}
