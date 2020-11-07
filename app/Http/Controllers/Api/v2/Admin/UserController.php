<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    const USER_COLUMNS = [
        'id',
        'name',
        'role',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function index()
    {
        return User::select(self::USER_COLUMNS)
            ->withTrashed()
            ->withCount(['articles' => fn ($q) => $q->withUserTrashed()->withTrashed()])
            ->get();
    }
    public function destroy(int $id)
    {
        tap(User::withTrashed()
        ->findOrFail($id), function ($u) {
            $u->deleted_at
                ? $u->restore()
                : $u->delete();
        });

        return $this->index();
    }
}
