<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {


    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 401);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $role = Role::where('name', 'patient')->first();
        // Attach the role with the model_type
        $user->roles()->attach($role->id, ['model_type' => User::class]);
        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'token' => $user->createToken('client')->plainTextToken,
        ], 200);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 401);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Email or password dose not match',
            ], 401);
        }
        $user = User::where('email', $request->email)->first();
        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully',
            'token' => $user->createToken('client')->plainTextToken,
        ]);
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
    public function user(Request $request) {
        if (Auth::check()) {
            // Retrieve the authenticated user
            $user = Auth::user();
            // Optionally load relationships
            $user->load('roles');

            return response()->json([
                'status' => true,
                'message' => 'User data',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated',
            ], 401);
        }
    }
}
