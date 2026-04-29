<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AttachmentController extends Controller
{
    public function index(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        $attachments = $user->myAttachments()->latest()->get();

        return response()->json([
            'data' => $attachments->map(fn (Attachment $a): array => [
                'id' => $a->id,
                'original_name' => $a->original_name,
                'is_image' => $a->is_image,
                'created_at' => $a->created_at?->toIso8601String(),
            ])->values()->all(),
        ]);
    }
}
