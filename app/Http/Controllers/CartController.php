<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        return view('cart.index', compact('cart', 'subtotal'));
    }

    public function add(Request $request)
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            $msg = 'Admin accounts are reserved for store management and cannot add products to cart.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('warning', $msg);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = (int) $request->get('quantity', 1);

        // Real E-Commerce Stock Validation
        if ($quantity > $product->stock) {
            $msg = "Only {$product->stock} units of '{$product->name}' are available in stock.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('warning', $msg);
        }

        $cart = session()->get('cart', []);

        $currentQtyInCart = isset($cart[$product->id]) ? $cart[$product->id]['quantity'] : 0;
        if (($currentQtyInCart + $quantity) > $product->stock) {
            $msg = "Cannot add more items. You already have {$currentQtyInCart} in cart and stock is {$product->stock}.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('warning', $msg);
        }

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->active_price,
                'original_price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->image,
                'category' => $product->category?->name ?? 'Hardware'
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson() || $request->ajax()) {
            $totalCount = array_sum(array_column($cart, 'quantity'));
            $subtotal = array_reduce($cart, function ($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);

            return response()->json([
                'success' => true,
                'message' => "{$product->name} added to cart!",
                'cart_count' => $totalCount,
                'subtotal' => number_format($subtotal, 2),
                'cart' => $cart
            ]);
        }

        return back()->with('success', "{$product->name} added to cart!");
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $cart      = session()->get('cart', []);
        $productId = $request->product_id;
        $quantity  = (int) $request->quantity;

        $product = Product::find($productId);
        if ($product && $quantity > $product->stock) {
            $msg = "Cannot update quantity to {$quantity}. Available stock is {$product->stock}.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('warning', $msg);
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Cart updated.']);
        }
        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required']);

        $cart      = session()->get('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Item removed from cart.']);
        }
        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $code = strtoupper(trim($request->coupon_code));
        $validCoupons = [
            'SESHOP2026' => ['type' => 'percent', 'value' => 20, 'name' => '20% Special Discount'],
            'SAVE10' => ['type' => 'percent', 'value' => 10, 'name' => '10% Hardware Deal'],
            'DEVBUILD' => ['type' => 'fixed', 'value' => 50.00, 'name' => '$50 Dev Credit'],
        ];

        if (!isset($validCoupons[$code])) {
            return back()->with('error', 'Invalid coupon code. Try SESHOP2026, SAVE10, or DEVBUILD.');
        }

        session()->put('coupon', [
            'code' => $code,
            'type' => $validCoupons[$code]['type'],
            'value' => $validCoupons[$code]['value'],
            'name' => $validCoupons[$code]['name'],
        ]);

        return back()->with('success', "Coupon '{$code}' applied! {$validCoupons[$code]['name']}");
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon code removed.');
    }

    public function clear()
    {
        session()->forget('cart');
        session()->forget('coupon');
        return redirect()->route('shop.index')->with('success', 'Shopping cart cleared.');
    }

    public function getCartJson()
    {
        $cart = session()->get('cart', []);
        $totalCount = array_sum(array_column($cart, 'quantity'));
        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        return response()->json([
            'cart' => array_values($cart),
            'cart_count' => $totalCount,
            'subtotal' => number_format($subtotal, 2)
        ]);
    }
}
