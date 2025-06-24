<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);


        $cart = auth()->user()->cart;

        if (!$cart) {
            $cart = auth()->user()->carts()->create();
        }

        $cartItem = $cart->items()->where('product_id', $request->product_id)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        $product = \App\Models\Product::find($request->product_id);
        if ($product) {
            $product->stock -= $request->quantity;
            $product->save();
        }

        if ($product && $product->stock < 0) {
            $product->stock += $request->quantity;
            $product->save();
            if ($cartItem) {
                $cartItem->delete();
            }

            return back()->with('flash', [
                'error' => 'Insufficient stock for the product: ' . $product->name
            ]);
        }

        return back()->with('flash', [
            'success' => 'Product added to cart successfully!'
        ]);
    }

}
