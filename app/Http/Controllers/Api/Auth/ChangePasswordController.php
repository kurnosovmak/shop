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


class ChangePasswordController extends Controller
{

    public function sendResetMail(Request $request)
    {
        $inputs = Validator::make($request->all(), [
            'captcha' => 'string|required',
            'captcha_key' => 'string|required',
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

        $user = Auth::user();
        if (!$user) {
            return response([
                'status' => '-1',
                'message' => 'User with with email not find',
            ], 200);
        }

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

        return response()->json([
            'status' => '1',
            'message' => 'Code send on email',
        ]);

    }

    public function sendResetSms(Request $request)
    {
        $inputs = Validator::make($request->all(), [
            'captcha' => 'string|required',
            'captcha_key' => 'string|required',
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
        $user = Auth::user();
        if (!$user) {
            return response([
                'status' => '-1',
                'message' => 'User with with email not find',
            ], 200);
        }


        SendSmsCodeJob::dispatch($user->phone);

        return response()->json([
            'status' => '1',
            'message' => 'Code send on email',
        ]);

    }

    public function changePassword(Request $request){

        $inputs = Validator::make($request->all(), [
            'email_code' => 'string|required',
            'sms_code' => 'string|required',
            'new_password' => 'string|required|min:8',
        ]);

        if ($inputs->fails()) {
            return response([
                'status' => '-1',
                'message' => $inputs->errors()->getMessages(),
            ], 200);
        }

        $user = Auth::user();
        if (!$user) {
            return response([
                'status' => '-1',
                'message' => 'User with with email not find',
            ], 200);
        }
        $lastEmailCode = ResetCode::where('login', $user->email)->orderBy('id', 'desc')->first();

        $lastSmsCode = RegistrationCode::where('email', $user->phone)->orderBy('id', 'desc')->first();

        if(!$lastEmailCode ||!$lastSmsCode){

            return response([
                'status'=>'-1',
                'message'=>'Code not find',
            ],301);
        }
        $lastEmailCode->input_count +=-1;
        $lastEmailCode->save();
        if($lastEmailCode->input_count<=0){
            if($lastEmailCode)$lastEmailCode->delete();
            if($lastSmsCode)$lastSmsCode->delete();
            return response([
                'status'=>'-1',
                'message'=>'attempts ended',
            ],301);
        }


//        Log::info($lastEmailCode->code.' '.$lastSmsCode->code);
        if( $lastEmailCode->code != $request->email_code|| $lastSmsCode->code != $request->sms_code){

            return response([
                'status'=>'-1',
                'message'=>'Incorrect code',
            ],301);
        }

        $user = Auth::user();
        $user->password = Hash::make($request['new_password']);
        $user->save();

        ResetCode::where('login', $user->email)->delete();
        RegistrationCode::where('email', $user->phone)->delete();
        return response([
            'status'=>'',
            'message'=>'Password update',
            'user'=>(new Profile(Auth::user()))->toArray($request)
        ],301);

    }


    public function getNumberAttempts(Request $request)  // Получаем оставшиеся количество попыток
    {

        $reset_code = ResetCode::where('login', Auth::user()->email)->first();
        if(!$reset_code){
            return response([
                'status'=>'-1',
                'message'=>'Code not find',
            ],200);
        }
        return response([
            'status'=>'1',
            'code'=>$reset_code->input_count,
        ],200);
    }
}
