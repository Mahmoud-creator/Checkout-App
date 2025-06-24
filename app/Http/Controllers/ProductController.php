<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{

    public function index()
    {
        $products = \App\Models\Product::all();

        // Return the inertia with the products
        return Inertia::render('Dashboard', [
            'products' => $products,
        ]);
    }

}
