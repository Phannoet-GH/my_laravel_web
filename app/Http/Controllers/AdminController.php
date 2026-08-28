<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalSales = Order::whereNotIn('status', ['cancelled'])->sum('total_amount');
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $totalUsers = User::count();
        $pendingOrders = Order::whereIn('status', ['pending', 'processing'])->count();
        $lowStockProducts = Product::where('stock', '<=', 5)->get();

        $allOrders = Order::with(['items.product', 'user'])->latest()->get();
        $recentOrders = $allOrders->take(10);
        $products = Product::with('category')->latest()->get();
        $categories = Category::all();

        return view('admin.dashboard', compact(
            'totalSales',
            'ordersCount',
            'productsCount',
            'totalUsers',
            'pendingOrders',
            'lowStockProducts',
            'allOrders',
            'recentOrders',
            'products',
            'categories'
        ));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tagline' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|url',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . rand(100, 999);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_trending'] = $request->has('is_trending');
        $validated['rating'] = 5.00;
        $validated['review_count'] = 1;

        Product::create($validated);

        return back()->with('success', 'New product added to SE Shop catalog!');
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tagline' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|url',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_trending'] = $request->has('is_trending');

        $product->update($validated);

        return back()->with('success', 'Product updated successfully!');
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return back()->with('success', 'Product removed from catalog.');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Order #{$order->order_number} status updated to " . strtoupper($request->status));
    }
}
