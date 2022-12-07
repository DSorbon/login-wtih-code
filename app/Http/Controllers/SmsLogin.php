<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmCodeRequest;
use App\Http\Requests\SendCodeRequest;
use App\Http\Requests\SignInWithCodeRequest;
use App\Http\Resources\AuthResource;
use App\Models\ConfirmPhone;
use App\Models\User;
use Illuminate\Support\Carbon;

class SmsLogin extends Controller
{
    public function sendCode(SendCodeRequest $request)
    {
        $code = rand(1000,9999);

        ConfirmPhone::create($request->validated() + ['code' => $code]);

        return response()->json(['code' => $code]);
    }

    public function confirmCode(ConfirmCodeRequest $request)
    {
        $request->validated();

        $code = $this->checkCode($request->phone, $request->code);

        if (!$code['confirmed']) {
            return response(['message' => $code['message']], 400);
        }

        $user = User::wherePhone($request->phone)->first();

        if (!$user) {
            $user = User::create(['phone' => $request->phone, 'name' => '', 'email' => '', 'password' => '']);
        }

        $token = $user->createToken($request->phone)->plainTextToken;

        return new AuthResource($user, $token);
    }

    public function checkCode($phone, $code)
    {
        $confirmCode = false;
        $confirmPhone = ConfirmPhone::where('phone',$phone)->latest()->first();
        $message = 'Код неверное';

        if ($confirmPhone) {
            if ($confirmPhone->qty > 5) {
                $message = 'Слишком много попыток, нажмите на повторное отправке кода!';
            } else if ($confirmPhone->updated_at < Carbon::parse()->now()->subMinutes(15)->format('Y-m-d H:i:s')) {
                $message = 'Код больше не жизнеспособен, нажмите на повторное отправке кода!';
            } else if (!$confirmPhone->confirmed) {
                $confirmCode = ConfirmPhone::wherePhone($phone)->whereCode($code)->latest()->first();
                $confirmPhone->update(['confirmed' => 1]);
            }
            $confirmPhone->update(['qty' => $confirmPhone->qty + 1]);
        }

        return ['confirmed' => $confirmCode, 'message' => $message];
    }
}
