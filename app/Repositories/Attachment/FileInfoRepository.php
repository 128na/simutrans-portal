<?php

namespace App\Repositories\Attachment;

use App\Models\Attachment\FileInfo;
use App\Repositories\BaseRepository;

/**
 * @extends BaseRepository<FileInfo>
 */
class FileInfoRepository extends BaseRepository
{
    /**
     * @var FileInfo
     */
    protected $model;

    public function __construct(FileInfo $model)
    {
        $this->model = $model;
    }
}
