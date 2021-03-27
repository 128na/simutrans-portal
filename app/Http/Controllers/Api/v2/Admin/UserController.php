<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UserStoreRequest;
use App\Jobs\Article\UpdateRelated;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        UpdateRelated::dispatchSync();

        return $this->index();
    }

    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();
        $len = 63;
        $rand = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-~^|,.', $len)), 0, $len);

        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($rand),
            'role' => config('role.user'),
        ]);
    }
}
