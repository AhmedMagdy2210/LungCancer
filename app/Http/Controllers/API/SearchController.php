<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Medicine;

class SearchController extends Controller {

    //search users
    public function searchUser(Request $request) {
        $users = User::query()->with(
            'roles',
        )->when($request->name, function ($query) use ($request) {
            $query->where('name', $request->name);
        })->when($request->email, function ($query) use ($request) {
            $query->where('email', $request->email);
        })->when($request->national_id, function ($query) use ($request) {
            $query->where('national_id', $request->national_id);
        })->get();
        return response()->json($users);
    }

    //search medicines
    public function serachMedicine(Request $request) {
        $medicines = Medicine::query()
            ->when($request->name, function ($query) use ($request) {
                $query->where('name', $request->name);
            })->get();
        return response()->json($medicines);
    }
}
