<?php

declare(strict_types=1);

namespace App\Models\Attachment;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperFileInfo
 */
final class FileInfo extends Model
{
    protected $fillable = [
        'attachment_id',
        'data',
    ];

    /**
     * @return array<string,string[]>
     */
    public function getDats(): array
    {
        return $this->data['dats'] ?? [];
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function getTabs(): array
    {
        return $this->data['tabs'] ?? [];
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }
}
