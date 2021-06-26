<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationOrders\CreateRequest;
use App\Http\Requests\RegistrationOrders\UpdateRequest;
use App\Models\RegistrationOrder;
use App\Repositories\UserRepository;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class RegistrationOrderController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create()
    {
        return view('registrationOrders.create');
    }

    public function store(CreateRequest $request)
    {
        $validated = $request->validated();

        if (!$this->canRegisterable($validated)) {
            return response('ご利用の環境からの新規登録はできません', 400);
        }

        RegistrationOrder::create([
            'email' => $validated['email'],
            'twitter' => $validated['twitter'],
            'name' => $validated['name'],
            'code' => $validated['code'],
            'request_info' => $this->getRequestInfo(),
        ]);

        return response('登録依頼を受け付けました<br><a href="/">Top</a>', 200);
    }

    private function canRegisterable(array $validated): bool
    {
        if (!Str::endsWith($validated['email'], '@gmail.com')) {
            return false;
        }

        if (!Str::contains(env('HTTP_USER_AGENT', ''), 'Mozilla')) {
            return false;
        }

        return true;
    }

    private function getRequestInfo(): string
    {
        return sprintf("%s\n%s\n%s\n",
            env('REMOTE_ADDR', '不明'),
            env('HTTP_REFERER', '不明'),
            env('HTTP_USER_AGENT', '不明')
        );
    }

    public function index()
    {
        $items = RegistrationOrder::orderBy('created_at', 'desc')->paginate(100);

        return view('registrationOrders.index', ['items' => $items]);
    }

    public function update(UpdateRequest $request, int $orderId)
    {
        $validated = $request->validated();

        $order = RegistrationOrder::findOrFail($orderId);
        // approveでの新規登録は初回のみ
        if ($order->status === RegistrationOrder::STATUS_APPROVAL) {
            return redirect()->route('registrationOrders.index');
        }
        DB::transaction(function () use ($validated, $order) {
            $order->update($validated);

            if ($validated['status'] === RegistrationOrder::STATUS_APPROVAL) {
                $len = random_int(31, 73);
                $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-~^|,.';
                $rand = substr(str_shuffle(str_repeat($chars, $len)), 0, $len);

                $user = $this->userRepository->store([
                    'name' => $order->name,
                    'email' => $order->email,
                    'password' => Hash::make($rand),
                    'role' => config('role.user'),
                ]);

                Password::broker()->sendResetLink(['email' => $user->email]);
            }
        });

        return redirect()->route('registrationOrders.index');
    }
}
