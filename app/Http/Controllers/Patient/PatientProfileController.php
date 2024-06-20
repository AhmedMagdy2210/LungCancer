<?php

namespace App\Http\Controllers\Patient;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PatientUserResource;
use App\Http\Resources\CancerResultResource;

class PatientProfileController extends Controller {

    public function update(Request $request, $id) {
        $patient = User::findOrFail($id);
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
        if ($patient->id === Auth::id()) {
            $patient->update($data);
            return response()->json($patient);
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    public function show($id) {

        $patient = User::with('cancerResults', 'roles')->findOrFail($id);
        if ($patient->id === Auth::id()) {
            return response()->json([
                'patient' => [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'gender' => $patient->gender,
                    'national_id' => $patient->national_id,
                    'born_date' => $patient->born_date,
                    'status' => RoleResource::collection($patient->roles),
                ]
            ]);
        } else {
            // Unauthorized access, return error response
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }
}
