<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use App\Http\Requests\Auth\RegistrationRequest;

class RegisterController extends Controller
{
    public function register(RegistrationRequest $request)
    {
        $newUser = $request->validated();

        $newUser['password'] = Hash::make($newUser['password']);
        $newUser['active'] = true;

        $user = User::create($newUser);

        $success = [
            'token' => $user->createToken('user',['app:all'])->plainTextToken,
            'name' => $user->name,
            'success' => true
        ];

        return response()->json($success, 200);
    }
}
