<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalSales = Order::whereNotIn('status', ['cancelled'])->sum('total_amount');
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $totalUsers = User::count();
        $customersCount = User::where('role', 'customer')->count();
        $adminsCount = User::where('role', 'admin')->count();
        $pendingOrders = Order::whereIn('status', ['pending', 'processing'])->count();
        $lowStockProducts = Product::where('stock', '<=', 5)->get();

        $allOrders = Order::with(['items.product', 'user'])->latest()->get();
        $recentOrders = $allOrders->take(10);
        $products = Product::with('category')->latest()->get();
        $categories = Category::all();

        // User Management: Directory with Search, Filter & Aggregations
        $userQuery = User::withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            }], 'total_amount');

        if ($request->filled('user_q')) {
            $userSearch = Str::substr(trim((string) $request->user_q), 0, 100);
            $userQuery->where(function ($q) use ($userSearch) {
                $q->where('name', 'like', "%{$userSearch}%")
                  ->orWhere('email', 'like', "%{$userSearch}%")
                  ->orWhere('phone', 'like', "%{$userSearch}%");
            });
        }

        if ($request->filled('user_role') && in_array($request->user_role, ['customer', 'admin'])) {
            $userQuery->where('role', $request->user_role);
        }

        $users = $userQuery->latest()->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'ordersCount',
            'productsCount',
            'totalUsers',
            'customersCount',
            'adminsCount',
            'pendingOrders',
            'lowStockProducts',
            'allOrders',
            'recentOrders',
            'products',
            'categories',
            'users'
        ));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'tagline' => 'nullable|string|max:200',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|url',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
        ]);

        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['tagline'] = !empty($validated['tagline']) ? strip_tags(trim($validated['tagline'])) : null;
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
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'tagline' => 'nullable|string|max:200',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|url',
        ]);

        $validated['name'] = strip_tags(trim($validated['name']));
        $validated['tagline'] = !empty($validated['tagline']) ? strip_tags(trim($validated['tagline'])) : null;
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

    /**
     * Register a new user directly from Admin Panel.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:128',
            'phone'    => 'nullable|string|max:30',
            'role'     => 'required|in:customer,admin',
        ]);

        $user = User::create([
            'name'     => strip_tags(trim($validated['name'])),
            'email'    => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'phone'    => !empty($validated['phone']) ? strip_tags(trim($validated['phone'])) : null,
            'role'     => $validated['role'],
        ]);

        return back()->with('success', "User account '{$user->name}' created successfully as " . strtoupper($user->role) . ".");
    }

    /**
     * Update user details (Name, Email, Phone, Role).
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'role'  => 'required|in:customer,admin',
        ]);

        // Safety: Do not demote yourself
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'You cannot remove admin privileges from your own active account.');
        }

        // Safety: Prevent removing the last admin
        if ($user->isAdmin() && $validated['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot demote the last remaining administrator account.');
        }

        $user->update([
            'name'  => strip_tags(trim($validated['name'])),
            'email' => strtolower(trim($validated['email'])),
            'phone' => !empty($validated['phone']) ? strip_tags(trim($validated['phone'])) : null,
            'role'  => $validated['role'],
        ]);

        return back()->with('success', "User account '{$user->name}' has been updated.");
    }

    /**
     * Quick role toggle/update for user.
     */
    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|in:customer,admin',
        ]);

        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'You cannot remove admin privileges from your own account.');
        }

        if ($user->isAdmin() && $request->role !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot demote the last remaining administrator.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Role for '{$user->name}' updated to " . strtoupper($request->role) . ".");
    }

    /**
     * Admin password reset for a user.
     */
    public function updateUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:8|max:128|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', "Password for '{$user->name}' has been reset successfully.");
    }

    /**
     * Delete user account.
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own active administrator account.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last remaining administrator.');
        }

        // Retain past order transactions by disassociating foreign key
        $user->orders()->update(['user_id' => null]);
        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User account '{$userName}' has been deleted.");
    }

    /**
     * Return user details and their order history as JSON for inspector modal.
     */
    public function getUserDetails($id)
    {
        $user = User::withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            }], 'total_amount')
            ->findOrFail($id);

        $orders = Order::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('customer_email', $user->email);
            })
            ->with('items')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'user'    => $user,
            'orders'  => $orders,
        ]);
    }
}
