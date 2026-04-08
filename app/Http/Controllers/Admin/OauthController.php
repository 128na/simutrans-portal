<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Oauth\AuthoroizeAction;
use App\Actions\Oauth\CallbackAction;
use App\Actions\Oauth\RefreshAction;
use App\Actions\Oauth\RevokeAction;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OauthController extends Controller
{
    public function index(): View
    {
        return view('admin.index');
    }

    public function authoroize(AuthoroizeAction $authoroizeAction): RedirectResponse
    {
        $authUrl = $authoroizeAction();

        return redirect($authUrl);
    }

    public function callback(Request $request, CallbackAction $callbackAction): RedirectResponse
    {
        $stringable = $request->string('state');
        $code = $request->string('code');
        $callbackAction($stringable->toString(), $code->toString());

        return to_route('admin.index');
    }

    public function refresh(RefreshAction $refreshAction): RedirectResponse
    {
        $refreshAction();

        return to_route('admin.index');
    }

    public function revoke(RevokeAction $revokeAction): RedirectResponse
    {
        $revokeAction();

        return to_route('admin.index');
    }
}
