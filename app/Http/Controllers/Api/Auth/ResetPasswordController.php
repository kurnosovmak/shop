<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Profile;
use App\Jobs\SendSmsCodeJob;
use App\Models\Auth\ResetCode;
use App\Models\User\RegistrationCode;
use App\Models\User\User;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class ResetPasswordController extends Controller
{

    public function sendResetByEmail(Request $request)
    {
        $inputs = Validator::make($request->all(), [
            'captcha' => 'string|required',
            'captcha_key' => 'string|required',
            'email' => 'string|required|exists:users,email',
        ]);

        if ($inputs->fails()) {
            return response([
                'status' => '-1',
                'message' => $inputs->errors()->getMessages(),
            ], 200);
        }
        if(!CaptchaService::checkCaptchaApi($request->captcha_key,$request->captcha)){
            return response([
                'status' => '-2',
                'message' => 'Captcha is wrong',
            ], 200);
        }


        $user = User::where([
            ['email', $request->email],
            ['status', User::STATUS_ACTIVE]
        ])->first();


        if (!$user) {
            return response([
                'status' => '-1',
                'message' => 'User with with email not find',
            ], 301);
        }

        $this->sendSmsAndEmailByUser($user);

        return response()->json([
            'status' => '1',
            'message' => 'Code send on email and phone',
        ]);

    }

    private function sendSmsAndEmailByUser($user)
    {
        $code = new ResetCode();
        $code->login = $user->email;
        $code->code = rand(1000000000, 9999999999);
        $code->save();

        $data = array('email' => $user->email,
            'theme' => 'Изменение пароля в Личном кабинете',
            'code' => $code->code);

//        Log::info('email code = '.$code->code);
        Mail::send([], [], function ($message) use ($data) {
            $message->subject($data['theme']);
            $message->to($data['email']);
            $message->setBody(view('templates.email.email', compact('data'))->render(), 'text/html');
        });
        SendSmsCodeJob::dispatch($user->phone);

    }

    public function sendResetByPhone(Request $request)
    {
        $inputs = Validator::make($request->all(), [
            'captcha' => 'string|required',
            'captcha_key' => 'string|required',
            'phone' => 'numeric|required|exists:users,phone',
        ]);

        if ($inputs->fails()) {
            return response([
                'status' => '-1',
                'message' => $inputs->errors()->getMessages(),
            ], 301);
        }
        if(!CaptchaService::checkCaptchaApi($request->captcha_key,$request->captcha)){
            return response([
                'status' => '-2',
                'message' => 'Captcha is wrong',
            ], 301);
        }



        $user = User::where([
            ['phone', $request->phone],
            ['status', User::STATUS_ACTIVE]
        ])->first();


        if (!$user) {
            return response([
                'status' => '-1',
                'message' => 'User with with email not find',
            ], 301);
        }

        $this->sendSmsAndEmailByUser($user);

        return response()->json([
            'status' => '1',
            'message' => 'Code send on email and phone',
        ]);

    }

    public function changePassword(Request $request)
    {

        $inputs = Validator::make($request->all(), [
            'email_code' => 'string|required',
            'sms_code' => 'string|required',
            'new_password' => 'string|required|min:8',
            'email' => 'string|exists:users,email',
            'phone' => 'numeric|exists:users,phone',
        ]);

        if ($inputs->fails()) {
            return response([
                'status' => '-1',
                'message' => $inputs->errors()->getMessages(),
            ], 301);
        }


        if (isset($request['email']) && isset($request['phone'])) {
            return response([
                'status' => '-1',
                'message' => 'It can not be immediately by email or by phone',
            ], 301);
        } else if (!isset($request['email']) && !isset($request['phone'])) {
            return response([
                'status' => '-1',
                'message' => 'It must be either an email or a phone number',
            ], 301);
        }
        $user = isset($request['email'])
            ? User::where('email', $request['email'])->first()
            : User::where('phone', $request['phone'])->first();

        if (!$user) {
            return response([
                'status' => '-1',
                'message' => 'User with with email not find',
            ], 301);
        }
        $lastEmailCode = ResetCode::where('login', $user->email)->orderBy('id', 'desc')->first();

        $lastSmsCode = RegistrationCode::where('email', $user->phone)->orderBy('id', 'desc')->first();

        if (!$lastEmailCode || !$lastSmsCode) {

            return response([
                'status' => '-1',
                'message' => 'Code not find',
            ], 301);
        }
        $lastEmailCode->input_count += -1;
        $lastEmailCode->save();
        if ($lastEmailCode->input_count <= 0) {
            if ($lastEmailCode) $lastEmailCode->delete();
            if ($lastSmsCode) $lastSmsCode->delete();
            return response([
                'status' => '-1',
                'message' => 'attempts ended',
            ], 301);
        }


        Log::info($lastEmailCode->code . ' ' . $lastSmsCode->code);
        if ($lastEmailCode->code != $request->email_code || $lastSmsCode->code != $request->sms_code) {

            return response([
                'status' => '-1',
                'message' => 'Incorrect code',
            ], 301);
        }

        $user = Auth::user();
        $user->password = Hash::make($request['new_password']);
        $user->save();

        ResetCode::where('login', $user->email)->delete();
        RegistrationCode::where('email', $user->phone)->delete();
        return response([
            'status' => '',
            'message' => 'Password update',
            'user' => (new Profile(Auth::user()))->toArray($request)
        ], 301);

    }


    public function getNumberAttempts(Request $request)  // Получаем оставшиеся количество попыток
    {
        $inputs = Validator::make($request->all(), [
            'email' => 'string|exists:users,email',
            'phone' => 'numeric|exists:users,phone',
        ]);

        if ($inputs->fails()) {
            return response([
                'status' => '-1',
                'message' => $inputs->errors()->getMessages(),
            ], 301);
        }

        if (isset($request['email']) && isset($request['phone'])) {
            return response([
                'status' => '-1',
                'message' => 'It can not be immediately by email or by phone',
            ], 301);
        } else if (!isset($request['email']) && !isset($request['phone'])) {
            return response([
                'status' => '-1',
                'message' => 'It must be either an email or a phone number',
            ], 301);
        }
        $user = isset($request['email'])
            ? User::where('email', $request['email'])->first()
            : User::where('phone', $request['phone'])->first();


        $reset_code = ResetCode::where('login', $user->email)->first();
        if (!$reset_code) {
            return response([
                'status' => '-1',
                'message' => 'Code not find',
            ], 200);
        }
        return response([
            'status' => '1',
            'code' => $reset_code->input_count,
        ], 200);
    }
}
