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

    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);

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

        $user = User::find($id);

        if ($user) {

            $user->update($request->all());

            $user = $user->refresh();

            return response()->json($user, 200);

        } else {
            return response()->json([
                'error' => "Not found user id: $id"
            ], 400);
        }

    }

    public function setStatus(Request $request, $id)
    {
        $request->merge(['id' => $id]);

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

        $user = User::find($id);

        if ($user) {

            $user->update($request->all());

            $user = $user->refresh();

            return response()->json(['id' => $user->id], 200);
        } else {
            return response()->json([
                'error' => "Not found user id: $id"
            ], 400);
        }

    }

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
}
