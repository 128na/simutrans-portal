<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Token\StoreRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    public function index(): View
    {
        $user = $this->loggedinUser();

        $tokens = PersonalAccessToken::where('tokenable_id', $user->id)
            ->where('tokenable_type', User::class)
            ->latest()
            ->get();

        return view('mypage.tokens', ['tokens' => $tokens]);
    }

    public function store(StoreRequest $storeRequest): RedirectResponse
    {
        $user = $this->loggedinUser();

        $validated = $storeRequest->validated();

        $newAccessToken = $user->createToken($validated['name']);

        return to_route('mypage.tokens.index')
            ->with('new_token', $newAccessToken->plainTextToken)
            ->with('status', 'APIトークンを発行しました');
    }

    public function destroy(int $tokenId): RedirectResponse
    {
        $user = $this->loggedinUser();

        PersonalAccessToken::where('id', $tokenId)
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', User::class)
            ->delete();

        return to_route('mypage.tokens.index')
            ->with('status', 'APIトークンを削除しました');
    }
}
