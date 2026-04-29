<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        $tokens = PersonalAccessToken::where('tokenable_id', $user->id)
            ->where('tokenable_type', User::class)
            ->latest()
            ->get();

        return view('mypage.tokens', ['tokens' => $tokens]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        /** @var User $user */
        $token = $user->createToken($validated['name']);

        return to_route('mypage.tokens.index')
            ->with('new_token', $token->plainTextToken)
            ->with('status', 'APIトークンを発行しました');
    }

    public function destroy(int $tokenId): RedirectResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        PersonalAccessToken::where('id', $tokenId)
            ->where('tokenable_id', $user->id)
            ->where('tokenable_type', User::class)
            ->delete();

        return to_route('mypage.tokens.index')
            ->with('status', 'APIトークンを削除しました');
    }
}
