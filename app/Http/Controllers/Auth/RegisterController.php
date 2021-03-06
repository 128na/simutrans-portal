<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mypage\User as UserResouce;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    private UserService $user_service;

    public function __construct(UserService $user_service)
    {
        $this->middleware('guest');

        $this->user_service = $user_service;
    }

    public function registerApi(Request $request)
    {
        $this->validator($request->all())->validate();

        if (config('app.register_restriction')) {
            $data = array_merge(
                $request->only('name', 'email'),
                [
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? '?',
                    'ua' => $_SERVER['HTTP_USER_AGENT'] ?? '?',
                ],
            );
            logger()->error('registerApi', $data);
            sleep(random_int(1, 5));

            return response(['message' => 'ご利用の環境からの新規登録はできません'], 400);
        }

        $this->register($request);

        $user = $this->user_service->getUser(Auth::user());

        return new UserResouce($user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => config('role.user'),
        ]);
    }
}
