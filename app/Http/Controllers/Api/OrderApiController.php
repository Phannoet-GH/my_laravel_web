<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderApiController extends Controller
{
    /**
     * List user orders (or all orders if admin).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $orders = Order::with(['items', 'user'])->latest()->paginate(20);
        } else {
            $orders = Order::with('items')
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15);
        }

        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ]);
    }

    /**
     * Get specific order details.
     */
    public function show(Request $request, $orderNumber): JsonResponse
    {
        $user = $request->user();

        $query = Order::with(['items', 'user'])
            ->where('order_number', $orderNumber)
            ->orWhere('id', $orderNumber);

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $order = $query->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Public order lookup by order number and email.
     */
    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $order = Order::with('items')
            ->where('order_number', strtoupper(trim($validated['order_number'])))
            ->where('customer_email', strtolower(trim($validated['email'])))
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'No matching order found for the provided details.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Place a new order via API.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'shipping_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'payment_method' => 'required|string|in:card,paypal,cod,bank_transfer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);

                if ($product->stock < $itemData['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product '{$product->name}'. Available stock: {$product->stock}.",
                    ], 422);
                }

                $unitPrice = $product->sale_price ?? $product->price;
                $itemSubtotal = $unitPrice * $itemData['quantity'];
                $subtotal += $itemSubtotal;

                // Decrement stock
                $product->decrement('stock', $itemData['quantity']);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Coupon discount calculation (e.g. WELCOME10 gives 10% off)
            $discount = 0;
            $couponCode = strtoupper($validated['coupon_code'] ?? '');
            if ($couponCode === 'WELCOME10') {
                $discount = round($subtotal * 0.10, 2);
            } elseif ($couponCode === 'SAVE20') {
                $discount = round($subtotal * 0.20, 2);
            }

            $tax = 0; // standard 0 or calculate as needed
            $totalAmount = max(0, $subtotal - $discount + $tax);

            $orderNumber = 'SE-ORD-' . strtoupper(Str::random(6));

            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'order_number' => $orderNumber,
                'customer_name' => $validated['customer_name'],
                'customer_email' => strtolower($validated['customer_email']),
                'customer_phone' => $validated['customer_phone'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'coupon_code' => $couponCode ?: null,
                'tax_amount' => $tax,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'status' => 'Pending',
                'tracking_code' => 'TRK-' . strtoupper(Str::random(8)),
            ]);

            foreach ($orderItems as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order->load('items'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order status (Admin).
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:Pending,Processing,Shipped,Delivered,Cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order,
        ]);
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, $orderNumber): JsonResponse
    {
        $user = $request->user();

        $query = Order::where('order_number', $orderNumber)->orWhere('id', $orderNumber);
        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        $order = $query->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        if (!$order->isCancelable()) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled in its current state (' . $order->status . ').',
            ], 400);
        }

        $order->update(['status' => 'Cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order,
        ]);
    }
}
