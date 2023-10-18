<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Hash;
use Validator;
use Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     operationId="register",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Registration data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Test User"),
     *             @OA\Property(property="email", type="string", format="email", example="test_user@test.com"),
     *             @OA\Property(property="password", type="string", format="password", example="your_password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="field", type="string"),
     *             @OA\Property(property="message", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Bad Request"),
     *             @OA\Property(property="message", type="string", example="The request was malformed or invalid."),
     *         )
     *     )
     *  )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'min:8']
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response()->json([
                'error' => $errors
            ], 400);
        }

        if ($validator->passes()) {

            User::exists() == 0 ? $request->merge(['role_id' => 1]) : $request->merge(['role_id' => 4]);

            if (!Role::find($request->role_id)) {
                return response()->json([
                    'error' => 'Role not exists.'
                ], 400);
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login user",
     *     operationId="login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Login data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email", example="test_user@test.com"),
     *             @OA\Property(property="password", type="string", format="password", example="your_password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User logined successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="field", type="string"),
     *             @OA\Property(property="message", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Bad Request"),
     *             @OA\Property(property="message", type="string", example="The request was malformed or invalid."),
     *         )
     *     )
     *  )
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Wrong email or password.'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/logout",
     *     summary="Logout User",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Bad Request"),
     *             @OA\Property(property="message", type="string", example="The request was malformed or invalid."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *             @OA\Property(property="message", type="string", example="User is not authenticated."),
     *         )
     *     ),
     * )
     */
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