<?php

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

class AttachmentRepository extends BaseRepository
{
    /**
     * @var Attachment
     */
    protected $model;

    public function __construct(Attachment $model)
    {
        $this->model = $model;
    }

    public function syncProfile(User $user, int $id): void
    {
        $user->profile->attachments()
            ->save($user->myAttachments()->find($id));
    }

    public function findAllByUser(User $user): Collection
    {
        return $user->myAttachments()
            ->select('id', 'original_name', 'path', 'attachmentable_id', 'attachmentable_type')
            ->get();
    }

    public function createFromFile(User $user, UploadedFile $file): Attachment
    {
        return $this->model->create([
            'user_id' => $user->id,
            'path' => Storage::disk('public')->putFile('user/'.$user->id, $file),
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function cursorCheckCompress(): LazyCollection
    {
        return $this->model->select('path')->cursor();
    }

    /**
     * @return LazyCollection<Attachment>
     */
    public function cursorZipFileAttachment(): LazyCollection
    {
        return $this->model
            ->select('attachments.*')
            ->where('attachments.path', 'like', '%.zip')
            ->cursor();
    }

    /**
     * @return LazyCollection<Attachment>
     */
    public function cursorPakFileAttachment(): LazyCollection
    {
        return $this->model
            ->select('attachments.*')
            ->where('attachments.original_name', 'like', '%.pak')
            ->cursor();
    }
}
