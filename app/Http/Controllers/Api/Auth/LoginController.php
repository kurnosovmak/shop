<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{

    public function login(Request $request)
    {
        $inputs = Validator::make($request->all(), [
            'phone' => 'numeric|required|exists:users,phone',
            'password'=>'string|required',
        ]);

        if($inputs->fails()){
            return response([
                'status'=>'-1',
                'message'=>$inputs->errors()->getMessages(),
            ],200);
        }

        $user = User::where('phone',$request->phone)->first();

        if(!$user){
            return response([
                'status'=>'-1',
                'message'=>'User not found',
            ],401);
        }

        $token = $user->createToken(config('app.name'));

        return response()->json([
            'status' => '1',
            'user' => $user,
            'token_type' => 'Bearer',
            'token' => $token->plainTextToken,
        ], 200);
    }

}
