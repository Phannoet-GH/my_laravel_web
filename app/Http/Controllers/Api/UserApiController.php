<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserApiController extends Controller
{
    /**
     * List registered users with optional search, role filter, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            }], 'total_amount');

        if ($request->filled('q')) {
            $search = Str::substr(trim((string) $request->input('q')), 0, 100);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->input('role'), ['customer', 'admin'])) {
            $query->where('role', $request->input('role'));
        }

        $perPage = max(1, min(100, (int) $request->input('per_page', 15)));
        $users = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
            ],
            'metrics' => [
                'total_users' => User::count(),
                'customers_count' => User::where('role', 'customer')->count(),
                'admins_count' => User::where('role', 'admin')->count(),
                'customers' => User::where('role', 'customer')->count(),
                'admins' => User::where('role', 'admin')->count(),
            ],
        ]);
    }

    /**
     * Register a new user account.
     */
    public function store(Request $request): JsonResponse
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

        return response()->json([
            'success' => true,
            'message' => 'User account created successfully',
            'data'    => $user,
        ], 201);
    }

    /**
     * Get specific user profile with recent order history.
     */
    public function show($id): JsonResponse
    {
        $user = User::withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            }], 'total_amount')
            ->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

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
            'data'    => [
                'user'   => $user,
                'orders' => $orders,
            ],
        ]);
    }

    /**
     * Update user details (name, email, phone, role).
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:100',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'role'  => 'sometimes|required|in:customer,admin',
        ]);

        // Safety: Prevent self-demotion
        if ($request->user() && $user->id === $request->user()->id && isset($validated['role']) && $validated['role'] !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You cannot remove administrator privileges from your own account.',
            ], 403);
        }

        // Safety: Prevent removing last admin
        if ($user->isAdmin() && isset($validated['role']) && $validated['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot demote the last remaining administrator.',
            ], 422);
        }

        $updates = [];
        if (isset($validated['name'])) {
            $updates['name'] = strip_tags(trim($validated['name']));
        }
        if (isset($validated['email'])) {
            $updates['email'] = strtolower(trim($validated['email']));
        }
        if (array_key_exists('phone', $validated)) {
            $updates['phone'] = !empty($validated['phone']) ? strip_tags(trim($validated['phone'])) : null;
        }
        if (isset($validated['role'])) {
            $updates['role'] = $validated['role'];
        }

        $user->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'User profile updated successfully',
            'data'    => $user,
        ]);
    }

    /**
     * Admin password reset.
     */
    public function updatePassword(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|max:128|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Password for user '{$user->name}' reset successfully.",
        ]);
    }

    /**
     * Delete user account.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        if ($request->user() && $user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own active administrator account.',
            ], 403);
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the last remaining administrator account.',
            ], 422);
        }

        $user->orders()->update(['user_id' => null]);
        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "User account '{$userName}' has been deleted.",
        ]);
    }
}
