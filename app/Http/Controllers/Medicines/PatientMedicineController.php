<?php

namespace App\Http\Controllers\Medicines;

use App\Models\User;
use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PatientMedicineController extends Controller {

    public function patientMedicine() {
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        foreach ($user->medicines() as $medicine) {
            $userMedicines = [
                'name' => $medicine->name,
                'description' => $medicine->description,
                'duration' => $medicine->duration,
            ];
        }
        return response()->json(['user' => $user, 'userMedicines' => $userMedicines]);
    }

    public function assignMedicine(Request $request) {
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        $medicine = Medicine::findOrFail(intval($request->input('medicine_id')));
        $duration = intval($request->input('duration'));
        $user->medicines()->attach($medicine->id, ['duration' => $duration]);
        return response()->json('assgind successfully');
    }
    public function updateMedicine(Request $request, $id) {
        $userId = Auth::id();
        $user = User::find($userId);
        $medicine = Medicine::findOrFail($id);
        $duration = intval($request->input('duration'));
        $user->medicines()->updateExistingPivot($medicine->id, ['duration' => $duration]);
        return response()->json('updated successfully');
    }
    public function deleteMedicine($id) {
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        $medicine = Medicine::findOrFail($id);
        $user->medicines()->detach($medicine->id);
        return response()->json('deleted successfully');
    }
}
