<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $ip
 * @property string|null $ua
 * @property string|null $referer
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read User $user
 *
 * @method static \Database\Factories\User\LoginHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginHistory query()
 *
 * @mixin \Eloquent
 */
class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'ua',
        'referer',
    ];

    protected static function booted(): void
    {
        static::creating(function (LoginHistory $loginHistory): void {
            $loginHistory->fill([
                'ip' => request()->server('REMOTE_ADDR'),
                'ua' => request()->server('HTTP_USER_AGENT'),
                'referer' => request()->server('HTTP_REFERER'),
            ]);
        });
    }

    /**
     * @return BelongsTo<User,LoginHistory>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
