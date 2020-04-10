<?php

namespace App\Http\Controllers;

use App\Services\RedirectService;
use Illuminate\Http\Request;

/**
 * 旧サイト -> 新サイトのリダイレクトを行う
 */
class RedirectController extends Controller
{
    /**
     * @var RedirectService
     */
    private $redirect_service;

    public function __construct(RedirectService $redirect_service)
    {
        $this->redirect_service = $redirect_service;
    }

    public function index(Request $request)
    {
        return $this->redirect_service->redirectOrFail($request);
    }
}
