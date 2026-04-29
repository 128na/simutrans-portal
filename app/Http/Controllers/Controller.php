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
    description: 'Simutrans Addon Portal API Documentation',
    title: 'Simutrans Portal API',
    contact: new OA\Contact(name: '128na', url: 'https://github.com/128na'),
    license: new OA\License(name: 'MIT'),
)]
#[OA\Server(
    url: \L5_SWAGGER_CONST_HOST,
    description: 'API Server'
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
