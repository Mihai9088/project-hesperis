<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;

class ProductController extends Controller
{
    public function home()
    {
        $products = Product::query()->published()->paginate(12);

        return Inertia::render('Home', [
            'products' => ProductListResource::collection($products)
        ]);
    }

    public function show(Product $product)
    {
       return Inertia::render('Product/Show', [
           'product' => new ProductResource($product),
           'variationOptions' =>request('options',[])
       ]); 

    }

}
