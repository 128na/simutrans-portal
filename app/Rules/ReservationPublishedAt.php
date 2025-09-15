<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

final class ReservationPublishedAt implements DataAwareRule, ValidationRule
{
    /**
     * @var array{article?:array{status?:string,published_at?:string}}
     */
    private array $data = [];

    public function __construct(
        private readonly CarbonImmutable $now,
    ) {}

    /**
     * @param  array{article?:array{status?:string,published_at?:string}}  $data
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
        if (!is_string($value)) {
            return;
        }

        if (!isset($this->data['article']['status'])) {
            return;
        }

        $status = ArticleStatus::tryFrom($this->data['article']['status']);
        if ($status !== ArticleStatus::Reservation) {
            return;
        }

        $carbonImmutable = new CarbonImmutable($value);
        if ($this->now->diffInMinutes($carbonImmutable) < 60) {
            $fail(':attribute は60分以上後の時刻を指定してください。');
        }
    }
}
