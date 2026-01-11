<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $from リダイレクト元
 * @property string $to リダイレクト先
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property int|null $user_id
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\RedirectFactory factory($count = null, $state = [])
 * @method static Builder<static>|Redirect from(string $from)
 * @method static Builder<static>|Redirect newModelQuery()
 * @method static Builder<static>|Redirect newQuery()
 * @method static Builder<static>|Redirect query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperRedirect
 */
class Redirect extends Model
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
