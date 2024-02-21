<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;

/**
 * @extends BaseRepository<Attachment>
 */
class AttachmentRepository extends BaseRepository
{
    /**
     * @var Attachment
     */
    protected $model;

    public function __construct(Attachment $attachment)
    {
        $this->model = $attachment;
    }

    public function syncProfile(User $user, int $id): void
    {
        $attachment = $user->myAttachments()->find($id);
        if (! $user->profile) {
            return;
        }

        if (! $attachment) {
            return;
        }

        $user->profile->attachments()->save($attachment);
    }

    public function findAllByUser(User $user): Collection
    {
        return $user->myAttachments()
            ->select('id', 'original_name', 'path', 'attachmentable_id', 'attachmentable_type')
            ->with('fileInfo')
            ->get();
    }

    /**
     * @return LazyCollection<int, Attachment>
     */
    public function cursorUnconvertedImages(): LazyCollection
    {
        return $this->model
            ->where(static function ($q): void {
                $q->orWhere('original_name', 'like', '%.png')
                    ->orWhere('original_name', 'like', '%.jpg')
                    ->orWhere('original_name', 'like', '%.jpeg');
            })
            ->where('path', 'not like', '%.webp')
            ->cursor();
    }

    public function createFromFile(User $user, UploadedFile $uploadedFile): Attachment
    {
        return $this->model->create([
            'user_id' => $user->id,
            'path' => Storage::disk('public')->putFile('user/'.$user->id, $uploadedFile),
            'original_name' => $uploadedFile->getClientOriginalName(),
        ]);
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
