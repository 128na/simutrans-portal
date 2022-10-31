<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Article\JobUpdateRelated;
use App\Repositories\UserRepository;

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
        $user = $this->userRepository->findOrFailWithTrashed($id);
        $this->userRepository->toggleDelete($user);

        JobUpdateRelated::dispatchSync();

        return $this->index();
    }
}
