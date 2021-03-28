<?php

namespace App\Repositories;

use App\Models\CompressedImage;

class CompressedImageRepository extends BaseRepository
{
    /**
     * @var CompressedImage
     */
    protected $model;

    public function __construct(CompressedImage $model)
    {
        $this->model = $model;
    }

    public function existsByPath(string $path): bool
    {
        return $this->model->where('path', $path)->exists();
    }
}
