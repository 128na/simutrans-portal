<?php

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return response(Auth::user()->projects()->get(), 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'credential' => 'required|json|max:10240',
        ]);

        return response(Auth::user()->projects()->create($data), 200);
    }

    public function update(Request $request, string $projectId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'credential' => 'required|json|max:10240',
        ]);

        return response(Auth::user()->projects()->findOrFail($projectId)->update($data), 200);
    }

    public function destroy(string $projectId)
    {
        return response(Auth::user()->projects()->findOrFail($projectId)->delete(), 200);
    }
}
