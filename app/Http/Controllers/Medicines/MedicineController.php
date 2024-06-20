<?php

namespace App\Http\Controllers\Medicines;

use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use function PHPUnit\Framework\isEmpty;

class MedicineController extends Controller {
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        $medicine = Medicine::create($request->all());
        return response()->json(['status' => 201,  'message' => 'Medicine Added successfuly', 'medicine' => $medicine]);
    }

    public function show($id) {
        $medicine = Medicine::findOrFail($id);
        return response()->json($medicine);
    }

    public function update(Request $request, $id) {
        $medicine = Medicine::findOrFail($id);
        // $request->validate([
        //     'name' => 'string',
        //     'description' => 'string',
        // ]);

        $data = $request->only(['name', 'description']);
        // dd($request);
        $data = array_filter($request->only(['name', 'description']), function ($value) {
            return $value !== null;
        });
        $medicine->update($data);
        return response()->json($medicine);
    }

    public function destroy($id) {
        Medicine::findOrFail($id)->delete();
        return response()->json(['status' => 201, 'message' => 'Medicine deleted successfuly']);
    }

    public function getAllMedicines() {
        $medicines = Medicine::all();
        return response()->json($medicines);
    }
}
