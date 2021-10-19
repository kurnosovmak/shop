<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Basket;
use App\Models\BasketItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{
    public function index()
    {
        $basket = Auth::user()->basket();

        $products = [];
        foreach ($basket->basketsItems as $product) {
            $products[] = [
                'count' => $product->count,
                'product' => $product->product
            ];
        }

        return response(
            [
                'status' => '1',
                'data' => $products,
            ]
            , 200);
    }

    public function update(Request $request)
    {
        $input = $request->validate([
            'product_id' => 'integer|min:0|exists:products,id|required',
            'count' => 'integer|required',
        ]);
        $basket = Auth::user()->basket();

        $basketItem = $basket->basketsItems->where('product_id', '=', $input['product_id'])->first();

        if ($basketItem != null) {
            $basketItem->count += $input['count'];
            if ($basketItem->count <= 0) {
                $basketItem->delete();
            } else {
                $basketItem->save();
            }
        } else if ($input['count'] > 0) {
            $basketItem = new BasketItem();
            $basketItem->product_id = $input['product_id'];
            $basketItem->basket_id = $basket->id;
            $basketItem->count = $input['count'];
            $basketItem->save();
        } else {
            return response([
                'status' => '-1',
                'message' => 'error count',
            ], 200);
        }
        return response(
            [
                'status' => '1',
//                'data' => $basket,
            ]
            , 200);
    }

    public function destroy()
    {
        $basket = Auth::user()->basket();

        if ($basket != null) {
            $basket->basketsItems()->delete();
        }
        return response(
            [
                'status' => '1',
            ]
            , 200);
    }

//    public function store(Request $request)
//    {
//        $inputs = $request->validate([
//            'basket_id' => 'integer|min:1|exists:baskets,id|required',
//            'contact' => 'string|required',
//        ]);
//
//        $basket = Auth::user() ?
//            Auth::user()->basket() :
//            Basket::create([]);
//
//
//        return $basket->basketsItems;
//
//    }

}
