<?php

namespace App\Http\Controllers\Api\Oauth;

use App\Http\Controllers\Controller;
use App\Models\Firebase\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Kreait\Firebase\Factory;
use Throwable;

class FirebaseController extends Controller
{
    private Factory $factory;
    private FirebaseAuth $auth;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
        $this->middleware(function (Request $request, $next) {
            try {
                $project = $request->route()->parameter('project');
                $this->auth = $this->factory
                    ->withServiceAccount($project->credential)
                    ->createAuth();
            } catch (Throwable $e) {
                report($e);

                return response(['error' => 'invalid credential'], 400);
            }

            return $next($request);
        });
    }

    public function login(Project $project)
    {
        $portalUser = Auth::user();

        try {
            $projectUser = $portalUser->projectUsers()->where('project_id', $project->id)->first();
            if (is_null($projectUser)) {
                throw new UserNotFound();
            }
            $firebaseUser = $this->auth->getUser($projectUser->uid);
        } catch (UserNotFound $e) {
            try {
                $firebaseUser = $this->auth->getUserByEmail($portalUser->email);
            } catch (UserNotFound $e) {
                $firebaseUser = $this->auth->createUser([
                    'email' => $portalUser->email,
                    'emailVerified' => (bool) $portalUser->email_verified_at,
                    'displayName' => $portalUser->name,
                    'disabled' => (bool) $portalUser->deleted_at,
                ]);
            }
        }
        $portalUser->projectUsers()->updateOrCreate(
            ['project_id' => $project->id],
            ['uid' => $firebaseUser->uid]
        );
        $customToken = $this->auth->createCustomToken($firebaseUser->uid, []);

        return response()->json([
            'custom_token' => $customToken->toString(),
        ]);
    }

    public function link(Project $project, Request $request)
    {
        try {
            $portalUser = Auth::user();
            $uid = $request->input('uid');
            $firebaseUser = $this->auth->getUser($uid);
            $portalUser->projectUsers()->updateOrCreate(
                ['project_id' => $project->id],
                ['uid' => $firebaseUser->uid]
            );

            return response(['result' => 'linked'], 200);
        } catch (UserNotFound $e) {
            return response(['result' => 'failed'], 400);
        }
    }

    public function unlink(Project $project)
    {
        try {
            $portalUser = Auth::user();
            $portalUser->projectUsers()->where('project_id', $project->id)->delete();

            return response(['result' => 'unlinked'], 200);
        } catch (UserNotFound $e) {
            return response(['result' => 'failed'], 400);
        }
    }
}
