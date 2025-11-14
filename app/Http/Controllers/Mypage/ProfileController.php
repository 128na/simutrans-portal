<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Http\Requests\User\UpdateRequest;
use App\Services\Front\MetaOgpService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\v2\Attachment;
use App\Http\Resources\v2\User as UserResouce;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        return view('v2.mypage.profile', [
            'user' => new UserResouce($user->load('profile')),
            'attachments' => Attachment::collection($user->myAttachments()->with('fileInfo')->get()),
            'meta' => $this->metaOgpService->profile(),
        ]);
    }

    public function update(UpdateRequest $updateRequest, UserService $userService): JsonResponse
    {
        $user = Auth::user();
        $userService->updateUserAndProfile($user, $updateRequest);
        return response()->json();
    }
}
