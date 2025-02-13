<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => ['required', 'min:2', 'max:50'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'max:30'],
            'phone' => ['required', 'min:7', 'max:20'],
            'address' => ['required', 'min:5'],
            'role_id' => ['nullable']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "message" => "unprocessable data",
                "errors" => $validator->errors(),
                "status" => 422
            ]);
        }
        $isExist = User::where('email', request('email'))->exists();
        if ($isExist) {
            return response()->json([
                "message" => "Email already exists"
            ], 422);
        }

        $user = User::create([
            "name" => request("name"),
            "email" => request("email"),
            "password" => request("password"),
            "phone" => request("phone"),
            "address" => request("address"),
            "role_id" => request("role_id") ?? 1
        ]);

        $token = $user->createToken("user-token")->plainTextToken;

        return response()->json([
            "message" => "user created",
            "token" => $token,
            "status" => 201
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'max:30']
        ]);
        if ($validator->fails()) {
            return response()->json([
                "message" => "Unprocessable data",
                "errors" => $validator->errors()
            ], 422);
        }

        $user = User::where('email', request('email'))->first();
        if (!$user) {
            return response()->json([
                "message" => "Email doesn't exists!"
            ], 422);
        }

        $isPassCorrect = Hash::check(request('password'), $user->password);
        if (!$isPassCorrect) {
            return response()->json([
                "message" => "Password isn't correct!"
            ], 422);
        }

        $token = $user->createToken("user-token")->plainTextToken;
        return response()->json([
            "message" => "Login success",
            "token" => $token
        ], 201);
    }

    public function profileImageUpdate(Request $request, User $user){
        $validator = Validator::make(request()->all(),[
            'profile'=>['required']
        ]);
        if($validator->fails()){
            return response()->json([
                'message'=>'unprocessable data',
                'error'=>$validator->errors()
            ],422);
        }

        if($user->id != request()->user()->id){
            return response()->json([
                'message'=>'you can\'t update another users\' profile'
            ],422);
        }

        $path = request("profile")->store("public");
        $url = Storage::url($path);

        $user->update([
            "profile"=>$url
        ]);
        return response()->json([
            'message'=>'profile updated successfully'
        ],200);
    }
}
