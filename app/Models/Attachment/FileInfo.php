<?php

declare(strict_types=1);

namespace App\Models\Attachment;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $attachment_id
 * @property array<array-key, mixed> $data
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FileInfo query()
 *
 * @mixin \Eloquent
 * @mixin IdeHelperFileInfo
 */
class FileInfo extends Model
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
