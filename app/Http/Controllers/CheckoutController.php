<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('warning', 'Admin accounts manage store fulfillment and cannot place customer orders.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('warning', 'Your shopping cart is empty.');
        }

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $coupon = session()->get('coupon');
        $discount = 0.00;
        if ($coupon) {
            if ($coupon['type'] === 'percent') {
                $discount = ($subtotal * $coupon['value']) / 100;
            } elseif ($coupon['type'] === 'fixed') {
                $discount = min($subtotal, $coupon['value']);
            }
        }

        $taxableAmount = max(0, $subtotal - $discount);
        $shipping = $taxableAmount > 500 ? 0.00 : 15.00;
        $tax = round($taxableAmount * 0.05, 2); // 5% estimated tax
        $total = max(0, $subtotal - $discount + $shipping + $tax);
        $user = auth()->user();

        return view('checkout.index', compact('cart', 'subtotal', 'discount', 'coupon', 'shipping', 'tax', 'total', 'user'));
    }

    public function process(Request $request)
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('warning', 'Admin accounts manage store fulfillment and cannot place customer orders.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop.index')->with('warning', 'Your shopping cart is empty.');
        }

        // Verify all cart item stock levels before committing transaction
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if (!$product || $product->stock < $item['quantity']) {
                $available = $product ? $product->stock : 0;
                return back()->withInput()->with('error', "Stock validation failed: '{$item['name']}' only has {$available} units remaining.");
            }
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|string|email|max:255',
            'customer_phone' => 'required|string|max:30',
            'shipping_address' => 'required|string|max:300',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'payment_method' => 'required|in:card,paypal,apple_pay,crypto'
        ]);

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $coupon = session()->get('coupon');
        $discount = 0.00;
        $couponCode = null;
        if ($coupon) {
            $couponCode = $coupon['code'];
            if ($coupon['type'] === 'percent') {
                $discount = ($subtotal * $coupon['value']) / 100;
            } elseif ($coupon['type'] === 'fixed') {
                $discount = min($subtotal, $coupon['value']);
            }
        }

        $taxableAmount = max(0, $subtotal - $discount);
        $shipping = $taxableAmount > 500 ? 0.00 : 15.00;
        $tax = round($taxableAmount * 0.05, 2);
        $total = max(0, $subtotal - $discount + $shipping + $tax);

        DB::beginTransaction();
        try {
            $orderNumber = 'SE-ORD-' . strtoupper(substr(uniqid(), 7, 6));

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'customer_name' => strip_tags(trim($validated['customer_name'])),
                'customer_email' => strtolower(trim($validated['customer_email'])),
                'customer_phone' => strip_tags(trim($validated['customer_phone'])),
                'shipping_address' => strip_tags(trim($validated['shipping_address'])),
                'city' => strip_tags(trim($validated['city'])),
                'postal_code' => strip_tags(trim($validated['postal_code'])),
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'coupon_code' => $couponCode,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'payment_method' => $validated['payment_method'],
                'status' => 'processing',
                'tracking_code' => 'TRK-' . rand(100000, 999999)
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Atomic stock decrement
                Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // Clear session cart and coupon
            session()->forget('cart');
            session()->forget('coupon');
            session()->put('recent_order', $order->order_number);

            return redirect()->route('orders.show', $order->order_number)
                ->with('success', 'Order placed successfully! Stock updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Order checkout processing failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to process order. Please verify your payment details and try again.');
        }
    }
}
