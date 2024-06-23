<?php

namespace App\Http\Controllers\Control;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;

class MainController extends Controller {


    public function getAllUsers() {
        $users = User::role('patient')->get();
        return response()->json($users);
    }
}
