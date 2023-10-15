<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use Validator;
use Auth;
use App\Models\User;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'min:8'],
            'role_id'  => ['required', 'integer']
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response()->json([
                'error' => $errors
            ], 400);
        }

        if ($validator->passes()) {

            if (User::exists() == 0) {

                $request->request->set('role_id', 1);

            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'active'   => true
            ]);

            $user->userRole()->create([
                'id_role' => $request->role_id
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]);
        }
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Wrong email or password.'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    public function logout()
    {
        if (auth()->check()) {
            $currentToken = auth()->user()->currentAccessToken();

            if ($currentToken) {

                $currentToken->delete();

                return response()->json([
                    'message' => 'Logged out Successeful.'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Token not found.'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 400);
        }

    }

}
