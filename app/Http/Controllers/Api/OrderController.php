<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Basket;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::all();
        return response(
            [
                'status' => '1',
                'data' => $orders,
            ]
            , 200);
    }

//    public function update(){
//
//    }

    public function destroy()
    {

    }

    public function store(Request $request)
    {
        $inputs = $request->validate([
//            'basket_id'=>'integer|min:1|exists:baskets,id|required',
            'contact' => 'string|required',
        ]);

        $order = new Order();
        $order->contact = $inputs['contact'];
        $order->save();

        $basket = Basket::latest()->first();

        $basketsItems = $basket->basketsItems;
        if(count($basketsItems) <= 0){
            return response([
                'status'=>'-1',
                'message'=>'basket is clear',
            ],200);
        }

        foreach ($basketsItems as $item) {
            $orderItem = new OrderItem();
            $orderItem->product_id = $item->product->id;
            $orderItem->title = $item->product->title;
            $orderItem->body = $item->product->body;
            $orderItem->image = $item->product->image;
            $orderItem->price = $item->product->price;
            $orderItem->order_id = $order->id;
            $orderItem->save();
        }
        if($basket != null){
            $basket->basketsItems()->delete();
        }
        return response(
            [
                'status' => '1'
            ], 200);

    }

    public function show(Order $order)
    {

        return response(
            [
                'status' => '1',
                'data' => $order,
            ]
            , 200);
    }
}
