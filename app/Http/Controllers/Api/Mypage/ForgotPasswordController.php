<?php

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */
    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // ログイン中ユーザーも利用できるようにする
        // $this->middleware('guest');
    }

    /**
     * @param  Response  $response
     */
    protected function sendResetLinkResponse(Request $request, $response): Response
    {
        return response('');
    }

    /**
     * @param  Response  $response
     */
    protected function sendResetLinkFailedResponse(Request $request, $response): Response
    {
        // invalid user etc
        return response('');
    }
}
