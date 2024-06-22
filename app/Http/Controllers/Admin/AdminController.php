<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;

class AdminController extends Controller {
    //moderators control
    public function assginModerator(User $user) {
        $user->roles()->detach();
        $role = Role::where('name', 'moderator')->first();
        // Attach the role with the model_type
        $user->roles()->attach($role->id, ['model_type' => User::class]);
        return response()->json(['status' => 200, 'message' => 'User is now moderator']);
    }

    public function showModerator($id) {

        $moderator = User::with('roles')->findOrFail($id);
        return response()->json([
            'patient' => [
                'id' => $moderator->id,
                'name' => $moderator->name,
                'email' => $moderator->email,
                'gender' => $moderator->gender,
                'national_id' => $moderator->national_id,
                'born_date' => $moderator->born_date,
                'status' => RoleResource::collection($moderator->roles),
            ]
        ]);
    }

    public function getAllModerators() {
        $usersWithModeratorRole = User::role('moderator')->get();
        return response()->json($usersWithModeratorRole);
    }

    public function deletemMderator($id) {
        User::findOrFail($id)->delete();
        return response()->json(['status' => 201, 'message' => 'Modertor deleted successfuly']);
    }

    //get date

}
