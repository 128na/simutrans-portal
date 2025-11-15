<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperRedirect
 */
final class Redirect extends Model
{
    /** @use HasFactory<\Database\Factories\RedirectFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from',
        'to',
    ];

    /**
     * @return BelongsTo<User,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
     |--------------------------------------------------------------------------
     | スコープ
     |--------------------------------------------------------------------------
     */
    /**
     * @param  Builder<Redirect>  $builder
     */
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function from(Builder $builder, string $from): void
    {
        $builder->where('from', $from);
    }
}
