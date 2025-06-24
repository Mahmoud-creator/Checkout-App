<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the cart items.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $cart = auth()->user()->cart;

        if (!$cart) {
            return Inertia::render('Cart/Index', [
                'items' => [],
                'total' => 0,
            ]);
        }

        $items = $cart->items()->with('product')->get();
        $total = $items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return Inertia::render('Cart', [
            'items' => $items,
            'total' => $total,
        ]);
    }

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

    public function updateQuantity(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = auth()->user()->cart;
        if (!$cart) {
            return back()->with('flash', [
                'error' => 'Cart not found.'
            ]);
        }

        $cartItem = $cart->items()->where('id', $itemId)->first();
        if (!$cartItem) {
            return back()->with('flash', [
                'error' => 'Cart item not found.'
            ]);
        }

        $product = $cartItem->product;
        if (!$product) {
            return back()->with('flash', [
                'error' => 'Product not found.'
            ]);
        }

        $diff = $request->quantity - $cartItem->quantity;

        if ($product->stock < $diff) {
            return back()->with('flash', [
                'error' => 'Insufficient stock for the product: ' . $product->name
            ]);
        }

        $product->stock -= $diff;
        $product->save();

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return back()->with('flash', [
            'success' => 'Cart updated successfully!'
        ]);
    }


    public function removeItem(Request $request, $itemId)
    {
        $cart = auth()->user()->cart;
        if (!$cart) {
            return back()->with('flash', ['error' => 'Cart not found.']);
        }

        $cartItem = $cart->items()->where('id', $itemId)->first();
        if (!$cartItem) {
            return back()->with('flash', ['error' => 'Cart item not found.']);
        }

        // Restore stock
        $product = $cartItem->product;
        if ($product) {
            $product->stock += $cartItem->quantity;
            $product->save();
        }

        $cartItem->delete();

        return back()->with('flash', [
            'success' => 'Item removed from cart.'
        ]);
    }

}
