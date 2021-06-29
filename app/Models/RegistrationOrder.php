<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'twitter',
        'name',
        'code',
        'request_info',
        'status',
        'rejected_reason',
    ];

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_APPROVAL = 'approval';

    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case self::STATUS_REJECTED:
                return 'bg-secondary';
            case self::STATUS_APPROVAL:
                return 'bg-info';
            case self::STATUS_PROCESSING:
            default:
                return '';
        }
    }
}
