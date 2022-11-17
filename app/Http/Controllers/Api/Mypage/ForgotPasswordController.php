<?php

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

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

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response('');
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        // invalid user etc
        return response('');
    }
}