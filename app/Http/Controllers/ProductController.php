<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InvoiceItem;

class ProductController extends Controller
{
    //
    public function allProducts()
    {
        // code...
        $products = Product::orderBy('id', 'DESC')->get();


        return  response()->json([
            'products' => $products
        ], 200);
    }

    
}
