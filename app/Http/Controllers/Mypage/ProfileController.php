<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\User\UpdateProfile;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\ProfileEdit;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        return view('mypage.profile', [
            'user' => new ProfileEdit($user->load('profile')),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'meta' => $this->metaOgpService->mypageProfile(),
        ]);
    }

    public function update(UpdateRequest $updateRequest, UpdateProfile $updateProfile): JsonResponse
    {
        $user = Auth::user();
        $updateProfile($user, $updateRequest);

        return response()->json();
    }
}
