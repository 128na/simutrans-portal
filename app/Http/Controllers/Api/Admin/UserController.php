<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Article\JobUpdateRelated;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserController extends Controller
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @return Collection<int, \App\Models\User>
     */
    public function index(): Collection
    {
        return $this->userRepository->findAllWithTrashed();
    }

    /**
     * @return Collection<int, \App\Models\User>
     */
    public function destroy(int $id): Collection
    {
        $user = $this->userRepository->findOrFailWithTrashed($id);
        $this->userRepository->toggleDelete($user);

        JobUpdateRelated::dispatchSync();

        return $this->index();
    }
}
