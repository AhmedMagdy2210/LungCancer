<?php

namespace App\Http\Controllers\Profile;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller {


    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        $stringDate = $request->born_date;
        $date = Carbon::createFromFormat('d-m-Y', $stringDate);
        $request->validate([
            'name' => 'string',
            'email' => 'string',
            'gender' => 'string',
            'national_id' => 'integer'
        ]);

        $data = array_merge($request->only(['name', 'email', 'gender', 'national_id']), ['born_date' => $date]);
        // dd($request);
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });
        if ($user->id === Auth::id()) {
            $user->update($data);
            return response()->json($user);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    public function show($id) {

        $user = User::with('roles')->findOrFail($id);
        if ($user->id === Auth::id()) {
            return response()->json([
                'patient' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'national_id' => $user->national_id,
                    'born_date' => $user->born_date,
                    'status' => RoleResource::collection($user->roles),
                ]
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }
}
