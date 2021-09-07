<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        return response([
            'status'=>'1',
            'data'=>Category::get(['id','type']),
        ],200);
    }
}
