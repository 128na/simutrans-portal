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
        /** @var array<string,string[]> */
        $dats = $this->data['dats'] ?? [];

        return $dats;
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function getTabs(): array
    {
        /** @var array<string,array<string,string>> */
        $tabs = $this->data['tabs'] ?? [];

        return $tabs;
    }

    /**
     * @return array<string,array<int,array<string,mixed>>>
     */
    public function getPaksMetadata(): array
    {
        /** @var array<string,array<int,array<string,mixed>>> */
        $paksMetadata = $this->data['paks_metadata'] ?? [];

        return $paksMetadata;
    }

    #[\Override]
    protected function casts(): array
    {
        return [
            'data' => 'json',
        ];
    }
}
