<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = Order::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('customer_email', $user->email);
            })
            ->with('items')
            ->latest()
            ->paginate(10);

        // Compute summary stats
        $allOrders = Order::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('customer_email', $user->email);
            })
            ->get();

        $totalSpent     = $allOrders->sum('total_amount');
        $totalOrders    = $allOrders->count();
        $activeOrders   = $allOrders->whereIn('status', ['pending', 'processing', 'shipped'])->count();
        $cancelledOrders = $allOrders->where('status', 'cancelled')->count();

        return view('user.dashboard', compact(
            'user', 'orders',
            'totalSpent', 'totalOrders', 'activeOrders', 'cancelledOrders'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:50',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password you entered is incorrect.'])->with('tab', 'security');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully! Please use your new password next time.')->with('tab', 'security');
    }
}

