<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Actions\Redirect\DeleteRedirect;
use App\Actions\Redirect\FindMyRedirects;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mypage\RedirectResource;
use App\Models\Redirect;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class RedirectController extends Controller
{
    public function index(FindMyRedirects $findMyRedirects): AnonymousResourceCollection
    {
        return RedirectResource::collection($findMyRedirects($this->loggedinUser()));
    }

    public function destroy(Redirect $redirect, DeleteRedirect $deleteRedirect, FindMyRedirects $findMyRedirects): AnonymousResourceCollection
    {
        $this->authorize('update', $redirect);
        $deleteRedirect($redirect);

        return RedirectResource::collection($findMyRedirects($this->loggedinUser()));
    }
}
