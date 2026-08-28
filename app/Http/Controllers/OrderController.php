<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items.product')->firstOrFail();
        return view('checkout.success', compact('order'));
    }

    public function lookup(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'order_number' => 'required|string',
                'customer_email' => 'required|email'
            ]);

            $order = Order::where('order_number', trim($request->order_number))
                ->where('customer_email', trim($request->customer_email))
                ->first();

            if (!$order) {
                return back()->withInput()->with('error', 'No matching order found. Please verify order number and email.');
            }

            return redirect()->route('orders.show', $order->order_number);
        }

        return view('checkout.lookup');
    }

    public function invoice($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with('items.product')->firstOrFail();

        // Only order owner or admin can view invoice
        if (auth()->check() && !auth()->user()->isAdmin()) {
            if ($order->user_id && $order->user_id !== auth()->id()) {
                abort(403, 'You are not authorized to view this invoice.');
            }
        } elseif (!auth()->check()) {
            // Guest: verify by email via session or deny
            abort(403, 'Please log in to view invoices.');
        }

        return view('orders.invoice', compact('order'));
    }

    public function cancel($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if (auth()->check() && !auth()->user()->isAdmin()) {
            // If order belongs to a specific user and it's not the current user, deny
            if ($order->user_id && $order->user_id !== auth()->id()) {
                abort(403, 'Unauthorized order action.');
            }
        }

        if (!$order->isCancelable()) {
            return back()->with('error', "Order {$order->order_number} cannot be cancelled because it is already {$order->status}.");
        }

        $order->update(['status' => 'cancelled']);

        // Restore stock
        foreach ($order->items as $item) {
            if ($item->product_id) {
                \App\Models\Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }

        return back()->with('success', "Order {$order->order_number} has been cancelled and stock restored.");
    }
}
