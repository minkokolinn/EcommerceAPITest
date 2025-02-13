<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            "name"=>["required"]
        ]);
        if($validator->fails()){
            return response()->json([
                "error"=>$validator->errors()
            ]);
        }
        Role::create([
            "name"=>request("name")
        ]);
        return response()->json([
            "message"=>"New Role Created..."
        ],201);
    }

    public function delete(Request $request, Role $role){
        $role->delete();
        return response()->json([
            "message"=>"role deleted.."
        ],200);
    }
}
