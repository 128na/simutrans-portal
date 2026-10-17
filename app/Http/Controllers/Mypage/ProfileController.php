<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\User\UpdateProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\ProfileEdit;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.profile', [
            'user' => new ProfileEdit($user->load('profile')),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'meta' => $this->metaOgpService->mypageProfile(),
        ]);
    }

    /**
     * プロフィールを更新
     */
    public function update(UpdateRequest $updateRequest, UpdateProfile $updateProfile): JsonResponse
    {
        $user = $this->loggedinUser();

        $updateProfile($user, $updateRequest);

        return response()->json();
    }
}
