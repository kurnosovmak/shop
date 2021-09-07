<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $inputs = $request->validate([
            'search' => 'String|min:3',
            "categories" => "array",
            "categories.*" => "string|exists:categories,id",
        ]);

//        $where = [];
//
//        if(array_key_exists('search',$inputs)){
//            $where[] = ['title','like','%'.$inputs['search'].'%'];
//        }
//
//        if(array_key_exists('categories',$inputs) && count($inputs['categories']) > 0){
//            for($i = 0;$i<count($inputs['categories']);$i++){
//                $whereOr[] = ['categories.id','=',$inputs['categories'][$i]];
//            }
//        }

        $products = null;
        if (array_key_exists('search', $inputs) || array_key_exists('categories', $inputs) && count($inputs['categories']) > 0) {

            $products = Product::with('productCategory');
            if (array_key_exists('search', $inputs)) {
                $products = $products->where('title', 'like', '%' . $inputs['search'] . '%')
                    ->orWhere('body', 'like', '%' . $inputs['search'] . '%');
            }
            if (array_key_exists('categories', $inputs) && count($inputs['categories']) > 0) {
                $ProductsCat = (ProductCategory::whereIn('category_id', $inputs['categories'])->get(['product_id']));
                $ids=[];
                foreach ( $ProductsCat as $prod ){
                    $ids[] = $prod->product_id;
                }
                $ProductsCat = null;

                $products = $products->whereIn('id', $ids);
            }
            $products = $products->paginate();
        } else {
            $products = Product::paginate();
        }
        $products = $products;


        return response(array_merge([
            'status' => '1',
        ], $products->toArray()), 200);
    }

    public function show(Request $request, Product $product)
    {


        return response([
            'status' => '1',

            'data' => $product
        ], 200);
    }
}
