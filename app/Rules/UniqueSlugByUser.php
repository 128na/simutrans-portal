<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Article;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

final class UniqueSlugByUser implements DataAwareRule, ValidationRule
{
    /**
     * @var array{article?:array{id?:int}}
     */
    private array $data = [];

    /**
     * @param  array{article?:array{id?:int}}  $data
     */
    #[\Override]
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    #[\Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            $fail('user not logged in');

            return;
        }

        if ($user->isAdmin()) {
            $this->passedForAdmin($value, $fail);
        } else {
            $this->passedForUser($user, $value, $fail);
        }
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    private function passedForAdmin(mixed $value, Closure $fail): void
    {
        $builder = Article::query()->where('slug', $value);
        // exclude myself
        if (isset($this->data['article']['id'])) {
            $builder->where('id', '<>', $this->data['article']['id']);
        }

        if ($builder->exists()) {
            $fail('validation.unique')->translate();
        }
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    private function passedForUser(User $user, mixed $value, Closure $fail): void
    {
        $adminIds = User::admin()->pluck('id');

        $existsInAdmin = Article::query()
            ->whereIn('user_id', $adminIds)
            ->where('slug', $value)
            ->exists();
        if ($existsInAdmin) {
            $fail('validation.uniqueAdmin')->translate();

            return;
        }

        $builder = Article::query()->where('user_id', $user->id)->where('slug', $value);
        // exclude myself
        if (isset($this->data['article']['id'])) {
            $builder->where('id', '<>', $this->data['article']['id']);
        }

        if ($builder->exists()) {
            $fail('validation.unique')->translate();

            return;
        }
    }
}
