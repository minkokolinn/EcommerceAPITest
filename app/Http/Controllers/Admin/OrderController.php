<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function show(Request $request){
        $orders = Order::with("user")->get();
        return response()->json([
            "orders"=>$orders
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make(request()->all(),[
            "total_amount"=>["required"],
            "address"=>["nullable"],
            "notes"=>["nullable"],
            "screen_shot"=>["nullable"],
            "order_products"=>["required"]
        ]);

        if($validator->fails()){
            return response()->json([
                "errors"=>$validator->errors()
            ]);
        }

        if(request("screen_shot")){
            $path = request("screen_shot")->store("public");
        }

        $order = Order::create([
            "status" => "pending",
            "total_amount"=>request("total_amount"),
            "address" => request("address")??request()->user()->address,
            "notes"=>request("notes"),
            "user_id"=>request()->user()->id,
            "screen_shot"=>$path??null
        ]);

        $order_products = request("order_products");
        foreach($order_products as $product){
            OrderProduct::create([
                "order_id" => $order->id,
                "product_id" => $product["product_id"],
                "quantity"=>$product["quantity"]
            ]);
        }

        return response()->json([
            "message"=>"Order created successfully",
            "order" => $order
        ]);
    }

    public function update(Request $request, Order $order){
        
        $validator = Validator::make(request()->all(),[
            "status" => ["required"]
        ]);
        if($validator->fails()){
            return response()->json([
                "errors"=>$validator->errors()
            ]);
        }

        $order->update([
            "status"=>request("status")
        ]);
        
        foreach($order->products as $product){
            $product->update([
                "quantity" => $product->quantity - $product->pivot->quantity
            ]);
        }

        return response()->json([
            "message"=>"Order updated successful",
            "order" => $order
        ]);
    }

    public function delete(Request $request, Order $order){
        $order->delete();
    }   
    public function detail(Request $request, Order $order){
        $order = Order::where("id",$order->id)->with(["user","products"])->get();
        return response()->json([
            "order"=>$order
        ]);
    }
}
