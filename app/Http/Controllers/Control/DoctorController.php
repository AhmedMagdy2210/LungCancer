<?php

namespace App\Http\Controllers\Control;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;

class DoctorController extends Controller {


    public function assginDoctor(User $user) {
        $user->roles()->detach();
        $user->assignRole('doctor');
        return response()->json(['status' => 200, 'message' => 'User is now doctor']);
    }



    public function getAllDoctors() {
        $usersWithDoctorRole = User::role('doctor')->get();
        return response()->json($usersWithDoctorRole);
    }


    public function show($id) {
        $doctor = User::with('roles')->findOrFail($id);
        return response()->json([
            'patient' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'email' => $doctor->email,
                'gender' => $doctor->gender,
                'national_id' => $doctor->national_id,
                'born_date' => $doctor->born_date,
                'specialization' => $doctor->specialization,
                'certification' => $doctor->certification,
                'status' => RoleResource::collection($doctor->roles),
            ]
        ]);
    }

    public function deleteDoctor($id) {
        User::findOrFail($id)->delete();
        return response()->json(['status' => 201, 'message' => 'Doctor deleted successfuly']);
    }
}
