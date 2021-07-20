<?php

namespace App\Http\Controllers\Api\Oauth;

use App\Exceptions\InvalidRedirectUrlException;
use App\Http\Controllers\Controller;
use App\Models\Firebase\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
                $this->validateRedirect($project, $request);
                $this->auth = $this->factory
                    ->withServiceAccount($project->credential)
                    ->createAuth();
            } catch (InvalidRedirectUrlException $e) {
                report($e);

                return response(['error' => 'Unauthorized access origin'], 401);
            } catch (Throwable $e) {
                report($e);

                return response(['error' => 'invalid credential'], 400);
            }

            return $next($request);
        });
    }

    private function validateRedirect(Project $project, Request $request): void
    {
        $referer = $request->header('referer');
        if (Str::startsWith($referer, $project->redirects)) {
            return;
        }
        throw new InvalidRedirectUrlException($referer);
    }

    public function showLogin()
    {
    }

    public function login(Project $project)
    {
        $user = Auth::user();

        try {
            $projectUser = $user->projectUsers()->where('project_id', $project->id)->first();
            if (is_null($projectUser)) {
                throw new UserNotFound();
            }
            $firebaseUser = $this->auth->getUser($projectUser->uid);
        } catch (UserNotFound $e) {
            try {
                $firebaseUser = $this->auth->getUserByEmail($user->email);
            } catch (UserNotFound $e) {
                $firebaseUser = $this->auth->createUser([
                    'email' => $user->email,
                    'emailVerified' => (bool) $user->email_verified_at,
                    'displayName' => $user->name,
                    'disabled' => (bool) $user->deleted_at,
                ]);
            }
        }
        $user->projectUsers()->updateOrCreate(
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
            $user = Auth::user();
            $uid = $request->input('uid');
            $firebaseUser = $this->auth->getUser($uid);
            $user->projectUsers()->updateOrCreate(
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
            $user = Auth::user();
            $user->projectUsers()->where('project_id', $project->id)->delete();

            return response(['result' => 'unlinked'], 200);
        } catch (UserNotFound $e) {
            return response(['result' => 'failed'], 400);
        }
    }
}
