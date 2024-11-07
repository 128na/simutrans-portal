<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperLoginHistory
 */
final class LoginHistory extends Model
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
