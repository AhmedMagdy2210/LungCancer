<?php

namespace App\Http\Controllers\Control;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;

class MainController extends Controller {

    
    public function getAllUsers() {
        $users = User::with('roles')->get();
        $usersWithRoles = $users->reject(function ($user) {
            return $user->hasRole('admin');
        })->map(function ($user) {
            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => RoleResource::collection($user->roles),
                ],
            ];
        });
        return response()->json(['status' => 200, 'users' => $usersWithRoles]);
    }
}
