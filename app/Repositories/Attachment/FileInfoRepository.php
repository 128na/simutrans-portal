<?php

declare(strict_types=1);

namespace App\Repositories\Attachment;

use App\Models\Attachment\FileInfo;

final readonly class FileInfoRepository
{
    public function __construct(private FileInfo $model) {}

    /**
     * @param  array<mixed>  $data
     */
    public function store(array $data): FileInfo
    {
        return $this->model->create($data);
    }

    /**
     * @param  array<mixed>  $search
     * @param  array<mixed>  $data
     */
    public function updateOrCreate(array $search, array $data = []): FileInfo
    {
        return $this->model->updateOrCreate($search, $data);
    }

    public function find(null|int|string $id): ?FileInfo
    {
        return $this->model->find($id);
    }

    /**
     * @param  array<mixed>  $data
     */
    public function update(FileInfo $fileInfo, array $data): void
    {
        $fileInfo->update($data);
    }

    public function delete(FileInfo $fileInfo): void
    {
        $fileInfo->delete();
    }
}
