<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class BasePolicy
{
    use HandlesAuthorization;

    protected function isSameUser(User $user, Model $model): bool
    {
        return property_exists($model, 'user_id') && $model->user_id !== null && $user->id === $model->user_id;
    }
}
