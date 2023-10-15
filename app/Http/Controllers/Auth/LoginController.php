<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(auth()->attempt($credentials)){

            $user = Auth::user();

            $user->tokens()->delete();

            $success = [
                'token' => $user->createToken($request->userAgent())->plainTextToken,
                'name' => $user->name,
                'success' => true
            ];

            return response()->json($success, 200);
        } else {

            return response()->json(['error'=>'Unauthorised'], 401);
        }

    }
}
