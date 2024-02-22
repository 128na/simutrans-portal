<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
