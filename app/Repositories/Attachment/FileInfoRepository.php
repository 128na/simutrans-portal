<?php

declare(strict_types=1);

namespace App\Repositories\Attachment;

use App\Models\Attachment\FileInfo;
use App\Repositories\BaseRepository;

/**
 * @extends BaseRepository<FileInfo>
 */
final class FileInfoRepository extends BaseRepository
{
    public function __construct(FileInfo $model)
    {
        parent::__construct($model);
    }
}
