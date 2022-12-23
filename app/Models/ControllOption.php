<?php

namespace App\Models;

use App\Constants\ControllOptionKeys;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControllOption extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'key';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'bool',
    ];

    private function isRestrict(string $key): bool
    {
        return $this->where(['key' => $key, 'value' => 0])->exists();
    }

    public function restrictLogin(): bool
    {
        return $this->isRestrict(ControllOptionKeys::LOGIN);
    }

    public function restrictRegister(): bool
    {
        return $this->isRestrict(ControllOptionKeys::REGISTER);
    }

    public function restrictInvitationCode(): bool
    {
        return $this->isRestrict(ControllOptionKeys::INVITATION_CODE);
    }

    public function restrictArticleUpdate(): bool
    {
        return $this->isRestrict(ControllOptionKeys::ARTICLE_UPDATE);
    }

    public function restrictTagUpdate(): bool
    {
        return $this->isRestrict(ControllOptionKeys::TAG_UPDATE);
    }
}
