<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['orderItems.product'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Orders', [
            'orders' => $orders,
        ]);
    }

    public function checkout(Request $request)
    {
        $user = $request->user();
        $cart = $user->cart;

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('flash', [
                'error' => 'Your cart is empty.'
            ]);
        }

        DB::beginTransaction();

        try {
            // Validate stock for all items
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                if (!$product || $product->stock < $cartItem->quantity) {
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('flash', [
                        'error' => 'Insufficient stock for product: ' . ($product ? $product->name : 'Unknown')
                    ]);
                }
            }

            // Deduct stock and create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $cart->items->sum(fn($item) => $item->product->price * $item->quantity),
            ]);

            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                $product->stock -= $cartItem->quantity;
                $product->save();

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price,
                ]);
            }

            $cart->items()->delete();

            DB::commit();

            return redirect()->route('orders.index')->with('flash', [
                'success' => 'Order placed successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')->with('flash', [
                'error' => 'Checkout failed. Please try again.'
            ]);
        }
    }

    public function cancel(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::where('id', $orderId)->where('user_id', $user->id)->first();

        if (!$order) {
            return redirect()->route('orders.index')->with('flash', [
                'error' => 'Order not found.'
            ]);
        }

        if ($order->status === 'shipped') {
            return redirect()->route('orders.index')->with('flash', [
                'error' => 'Order has already been shipped and cannot be cancelled.'
            ]);
        }

        DB::transaction(function () use ($order) {
            // Restore stock for each item
            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->stock += $item->quantity;
                    $item->product->save();
                }
            }
            $order->status = 'cancelled';
            $order->save();
        });

        return redirect()->route('orders.index')->with('flash', [
            'success' => 'Order cancelled successfully.'
        ]);
    }
}
