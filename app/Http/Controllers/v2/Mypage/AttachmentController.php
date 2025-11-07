<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2\Mypage;

use App\Actions\StoreAttachment\Store;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attachment\StoreRequest;
use App\Http\Resources\v2\Attachment as V2Attachment;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;
use Throwable;

final class AttachmentController extends Controller
{
    public function __construct() {}

    public function store(StoreRequest $storeRequest, Store $store): V2Attachment
    {
        /** @var Illuminate\Http\UploadedFile|null */
        $file = $storeRequest->file('file');
        if (!$file) {
            abort(400);
        }
        $attachment = $store(Auth::user(), $file, []);
        try {
            dispatch_sync(new \App\Jobs\Attachments\UpdateFileInfo($attachment));
        } catch (Throwable $throwable) {
            report($throwable);
        }

        return new V2Attachment($attachment);
    }

    public function destroy(Attachment $attachment): \Illuminate\Http\Response
    {
        if (Auth::user()->cannot('update', $attachment)) {
            return abort(403);
        }

        $attachment->delete();

        return response('');
    }
}
