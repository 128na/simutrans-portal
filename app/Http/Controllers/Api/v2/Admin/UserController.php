<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\UserStoreRequest;
use App\Jobs\Article\JobUpdateRelated;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return $this->userRepository->findAllWithTrashed();
    }

    public function destroy(int $id)
    {
        $user = $this->userRepository->findWithTrashed($id);
        $this->userRepository->toggleDelete($user);

        JobUpdateRelated::dispatchSync();

        return $this->index();
    }

    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();
        $len = random_int(31, 73);
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-~^|,.';
        $rand = substr(str_shuffle(str_repeat($chars, $len)), 0, $len);

        return $this->userRepository->store([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($rand),
            'role' => config('role.user'),
        ]);
    }
}
