<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        return response(Auth::user()->projects()->get(), 200);
    }

    private function validateProject(Request $request): array
    {
        $redirectRule = function ($attribute, $value, $fail) {
            foreach (explode(',', $value) as $url) {
                if (Validator::make(['url' => $url], ['url' => 'required|url'])->fails()) {
                    return $fail(__('validation.url', ['attribute' => __("validation.attributes.$attribute")]));
                }
            }
        };

        return $request->validate([
            'name' => 'required|string|max:255',
            'redirect' => ['required', $redirectRule],
            'credential' => 'required|json|max:10240',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateProject($request);

        return response(Auth::user()->projects()->create($data), 200);
    }

    public function update(Request $request, string $projectId)
    {
        $data = $this->validateProject($request);

        return response(Auth::user()->projects()->findOrFail($projectId)->update($data), 200);
    }

    public function destroy(string $projectId)
    {
        return response(Auth::user()->projects()->findOrFail($projectId)->delete(), 200);
    }
}
