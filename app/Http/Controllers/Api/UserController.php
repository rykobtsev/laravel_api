<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Validator;
use App\Models\User;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(User::class, 'user');
    // }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="About user",
     *     operationId="aboutUser",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="role_id", type="integer"),
     *             @OA\Property(property="role", type="string"),
     *             @OA\Property(property="active", type="boolean"),
     *             @OA\Property(property="created", type="string", format="date-time"),
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
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role_id' => $user->userRole->role->id,
            'role'    => $user->userRole->role->name,
            'active'  => $user->active,
            'created' => $user->created_at
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Show user",
     *     operationId="User",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="role_id", type="integer"),
     *             @OA\Property(property="role", type="string"),
     *             @OA\Property(property="active", type="boolean"),
     *             @OA\Property(property="created", type="string", format="date-time"),
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
    public function show($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => ['required', 'integer']
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response()->json([
                'error' => $errors
            ], 400);
        }

        $user = User::find($id);

        if ($user) {
            return response()->json([
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'role_id' => $user->userRole->role->id ?? null,
                'role'    => $user->userRole->role->name ?? null,
                'active'  => $user->active,
                'created' => $user->created_at
            ], 200);
        } else {
            return response()->json([
                'message' => 'User not found.'
            ], 200);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/user",
     *     summary="Update user",
     *     operationId="updateUser",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1, description="User ID"),
     *             @OA\Property(property="name", type="string", example="User Name", description="User name"),
     *             @OA\Property(property="email", type="string", format="email", example="test_user@test.com", description="User email"),
     *             @OA\Property(property="password", type="string", example="your_password", description="User password"),
     *             @OA\Property(property="id_role", type="integer", example=2, description="User role ID"),
     *             required={"id"}
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="active", type="boolean"),
     *             @OA\Property(property="created", type="string", format="date-time"),
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
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'       => ['required', 'integer'],
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'unique:users', 'max:255'],
            'password' => ['sometimes', 'min:8'],
            'id_role'  => ['sometimes', 'integer'],
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response()->json([
                'error' => $errors
            ], 400);
        }

        $user = User::find($request->id);

        if ($user) {

            $user->update($request->all());

            $user = $user->refresh();

            return response()->json($user, 200);

        } else {
            return response()->json([
                'error' => "Not found user id: $request->id"
            ], 400);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/user/status",
     *     summary="Change user status",
     *     operationId="setStatusUser",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Status data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1, description="User ID"),
     *             @OA\Property(property="active", type="boolean", example=true, description="Status User"),
     *             required={"id","active"}
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Change status successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
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
    public function setStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'     => ['required', 'integer'],
            'active' => ['required', 'boolean']
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response()->json([
                'error' => $errors
            ], 400);
        }

        $user = User::find($request->id);

        if ($user) {

            $user->update($request->all());

            $user = $user->refresh();

            return response()->json(['id' => $user->id], 200);
        } else {
            return response()->json([
                'error' => "Not found user id: $request->id"
            ], 400);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/user/{id}",
     *     summary="Delete User",
     *     operationId="destroyUser",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
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
    public function destroy($id)
    {
        $currentUser = auth()->user();

        if ($currentUser->cannot('delete', $currentUser)) {
            return response()->json([
                'error' => "You do not have permission to delete a user."
            ], 403);
        }

        $validator = Validator::make(['id' => $id], [
            'id' => ['required', 'integer']
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response()->json([
                'error' => $errors
            ], 400);
        }

        $user = User::find($id);

        if ($user) {

            if ($currentUser->id === $user->id) {
                return response()->json([
                    'error' => "You can't delete yourself."
                ], 403);
            }

            $user->delete();

            return response()->json(['id' => $user->id], 200);

        } else {

            return response()->json([
                'error' => "Not found user id: $id"
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List users",
     *     operationId="usersList",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="role_id", type="integer"),
     *                 @OA\Property(property="role", type="string"),
     *                 @OA\Property(property="active", type="boolean"),
     *                 @OA\Property(property="created", type="string", format="date-time"),
     *             )
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
    public function list()
    {
        $currentUser = auth()->user();

        if ($currentUser->cannot('view', $currentUser)) {
            return response()->json([
                'error' => "You do not have permission to views a user."
            ], 403);
        }

        return User::all();
    }

    /**
     * @OA\Post(
     *     path="/api/users/find",
     *     summary="Search user",
     *     operationId="searchUser",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Search data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="User Name", description="User name"),
     *             @OA\Property(property="email", type="string", format="email", example="test_user@test.com", description="User email"),
     *             @OA\Property(property="role", type="string", example="Guest", description="User role"),
     *             required={"id"}
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search user successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="active", type="boolean"),
     *             @OA\Property(property="created", type="string", format="date-time"),
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
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email'],
            'role'  => ['sometimes', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {

            $errors = $validator->errors();

            return response()->json([
                'error' => $errors
            ], 400);
        }

        $query = User::query();

        if ($request->has('name')) {
            $query->where('name', 'like', "%{$request->name}%");
        }

        if ($request->has('email')) {
            $query->where('email', $request->email);
        }

        if ($request->has('role')) {
            $query->whereHas('userRole.role', function ($subquery) use ($request) {
                $subquery->where('name', $request->role);
            });
        }

        $results = $query->get();

        if ($results) {
            return response()->json($results, 200);
        } else {
            return response()->json(['message' => 'User not found.'], 200);
        }
    }
}