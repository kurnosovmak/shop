<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class RegisterController extends Controller
{

    public function register(Request $request)
    {
        $inputs = Validator::make($request->all(), [
            'phone' => 'numeric|required|unique:users,phone',
            'password' => 'string|required|min:8',
        ]);

        if ($inputs->fails()) {
            return response([
                'status' => '-1',
                'message' => $inputs->errors()->getMessages(),
            ], 200);
        }

        $user = new User();
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->verify_sms_token = md5($request->phone . Str::random(6));
        $user->status = User::STATUS_ACTIVE;
        $user->save();

        $token = $user->createToken(config('app.name'));
//
//        $token = Auth::user()->createToken(config('app.name'));
//        $token->token->expires_at = Carbon::now()->addMonth();
//
//        $token->token->save();

        return response()->json([
            'status' => '1',
            'user' => $user,
            'token_type' => 'Bearer',
            'token' => $token->plainTextToken,
        ], 200);
    }

    public function verifySms($token)
    {
        $user = User::where('verify_sms_token', $token)->first();
        if (!$user) {
            return response([
                'status' => '-1',
                'message' => 'The email could not be identified or is activated email',
            ], 200);
        }
        $user->verify_sms_token = null;
        $user->phone_verified_at = now();
        $user->save();

        return response([
            'status' => '1',
            'message' => 'Email verified',
        ], 200);
    }


}
