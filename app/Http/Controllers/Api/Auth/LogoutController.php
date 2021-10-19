<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class LogoutController extends Controller
{

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'status'=>'1',
            'message' => 'You are successfully logged out',
        ]);
    }
}
