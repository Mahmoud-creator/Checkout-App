<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Add a product to the cart.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            return response()->json(['error' => 'Insufficient stock for this product.'], 400);
        }

        return response()->json(['message' => 'Product added to cart successfully!']);
    }

}
