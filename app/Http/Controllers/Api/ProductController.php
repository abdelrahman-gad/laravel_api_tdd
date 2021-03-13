<?php

namespace App\Http\Controllers\Api;
use App\Http\Resources\Product as ProductResource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Str;
class ProductController extends Controller
{
 
    public function store(Request $request ){
        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' =>$request->price
        ]);
        return response()->json(new  ProductResource( $product) , 201);
    }

    public function show(int $id){
       $product = Product::findOrFail($id);
       return response()->json(new  ProductResource( $product) , 201);
    }
}
