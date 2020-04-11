<?php
namespace App\Services;

use App\Models\Redirect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RedirectService extends Service
{
    public function __construct(Redirect $model)
    {
        $this->model = $model;
    }

    public function redirectOrFail(Request $request)
    {
        $path = $this->getRelativePath($request);
        $redirect = $this->model->from($path)->firstOrFail();

        logger("[redirect]: {$path} -> {$redirect->to}");
        return redirect($redirect->to, 301);
    }

    private function getRelativePath($request)
    {
        return str_replace(config('app.url'), '', $request->fullUrl());
    }
}
