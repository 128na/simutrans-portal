<?php

declare(strict_types=1);

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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoginHistory query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperLoginHistory
 */
class LoginHistory extends Model
{
    /** @use HasFactory<\Database\Factories\User\LoginHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'ip',
        'ua',
        'referer',
    ];

    /**
     * @return BelongsTo<User,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[\Override]
    protected static function booted(): void
    {
        self::creating(function (LoginHistory $loginHistory): void {
            $loginHistory->fill([
                'ip' => request()->server('REMOTE_ADDR'),
                'ua' => request()->server('HTTP_USER_AGENT'),
                'referer' => request()->server('HTTP_REFERER'),
            ]);
        });
    }
}
