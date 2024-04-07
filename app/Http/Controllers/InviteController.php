<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;

final class InviteController extends Controller
{
    public function __construct()
    {
    }

    public function index(User $user): Renderable
    {
        return view('front.spa');
    }
}
