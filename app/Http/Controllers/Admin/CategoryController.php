<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
        $category = Category::create([
            "name"=>request("name")
        ]);
        return response()->json([
            "message"=>"category created...",
            "category"=>$category
        ],201);
    }

    public function update(Request $request, Category $category){
        $validator = Validator::make($request->all(),[
            "name"=>["required"]
        ]);
        if($validator->fails()){
            return response()->json([
                "error"=>$validator->errors()
            ]);
        }
        $category->update([
            "name"=>request("name")
        ]);
        return response()->json([
            "message"=>"category updated...",
            "category"=>$category
        ],200);
    }
    
    public function delete(Request $request, Category $category){
        $category->delete();
        return response()->json([
            "message"=>"delete successful"
        ],200);
    }

    public function show(Request $request){
        $categories = Category::all();
        return response()->json([
            "categories"=>$categories
        ]);
    }
}
