@extends('layouts.app')

@section('title', 'Admin Dashboard - SE Shop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Merchant Control Box Header -->
    <div class="glass-panel p-6 border-indigo-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-bold uppercase tracking-wider">
                <i class="bi bi-shield-lock-fill"></i> Admin Merchant Portal
            </div>
            <h1 class="text-3xl font-extrabold text-white mt-1.5">SE Shop Store Control</h1>
            <p class="text-xs text-gray-400 mt-1">Manage merchant products, live inventory, customer orders, and registered user accounts</p>
        </div>

        <div class="flex items-center flex-wrap gap-3">
            <button onclick="document.getElementById('add-user-modal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/40 text-xs font-bold flex items-center justify-center gap-2 shadow-lg shadow-purple-500/10 transition-all">
                <i class="bi bi-person-plus-fill text-sm"></i> Add New User
            </button>
            <button onclick="document.getElementById('add-product-modal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl cyber-glow-btn text-xs font-bold flex items-center justify-center gap-2 shadow-lg shadow-cyan-500/20">
                <i class="bi bi-plus-lg text-sm"></i> Add New Product
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-300 text-xs shadow-lg">
            <div class="font-bold mb-1 flex items-center gap-2">
                <i class="bi bi-exclamation-octagon-fill text-rose-400"></i> Please check the following issues:
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Analytics Stats Overview Grid (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Stat 1: Revenue -->
        <div class="stat-metric-card space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Revenue</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-base font-bold flex-shrink-0">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white">${{ number_format($totalSales, 2) }}</div>
            <span class="text-[10px] text-emerald-400 font-bold flex items-center gap-1"><i class="bi bi-graph-up-arrow"></i> Platform Sales</span>
        </div>

        <!-- Stat 2: Orders -->
        <div onclick="switchTab('orders')" class="stat-metric-card space-y-1 cursor-pointer hover:border-cyan-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Orders</span>
                <div class="w-9 h-9 rounded-xl bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-base font-bold flex-shrink-0">
                    <i class="bi bi-cart-check"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-cyan-400">{{ $ordersCount }}</div>
            <span class="text-[10px] text-gray-400">Processed orders &middot; <span class="text-cyan-400 underline">View list</span></span>
        </div>

        <!-- Stat 3: Registered Users -->
        <div onclick="switchTab('users')" class="stat-metric-card space-y-1 cursor-pointer hover:border-purple-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Registered Users</span>
                <div class="w-9 h-9 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-400 flex items-center justify-center text-base font-bold flex-shrink-0">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-purple-400">{{ $totalUsers }}</div>
            <span class="text-[10px] text-purple-300 font-semibold">{{ $customersCount }} Customers &middot; {{ $adminsCount }} Admins</span>
        </div>

        <!-- Stat 4: Fulfillment Queue -->
        <div class="stat-metric-card space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Fulfillment Queue</span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-base font-bold flex-shrink-0">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-400">{{ $pendingOrders }}</div>
            <span class="text-[10px] text-amber-300 font-semibold">Pending / Processing</span>
        </div>
    </div>

    @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
        <!-- Low Stock Inventory Alert Banner -->
        <div class="p-4 rounded-2xl bg-amber-950/80 border border-amber-500/40 text-amber-300 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-white text-sm">Low Stock Inventory Warning ({{ $lowStockProducts->count() }} SKUs)</h4>
                    <p class="text-amber-200/80 text-[11px]">The following SKUs have 5 or fewer items remaining in inventory: 
                        @foreach($lowStockProducts as $lowProd)
                            <span class="font-bold text-white bg-amber-900/60 px-2 py-0.5 rounded border border-amber-500/30 ml-1">{{ $lowProd->name }} ({{ $lowProd->stock }} left)</span>
                        @endforeach
                    </p>
                </div>
            </div>
            <button onclick="switchTab('products')" class="px-3 py-1.5 rounded-lg bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 font-bold text-xs whitespace-nowrap">
                Review Inventory
            </button>
        </div>
    @endif

    <!-- Navigation Tabs Switcher -->
    <div class="flex items-center gap-2 border-b border-gray-800 pb-3 overflow-x-auto">
        <button id="tab-btn-orders" onclick="switchTab('orders')" class="admin-tab-btn px-4 py-2.5 rounded-xl text-xs font-extrabold flex items-center gap-2 border transition-all">
            <i class="bi bi-receipt"></i> Orders Fulfillment
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">{{ count($allOrders) }}</span>
        </button>

        <button id="tab-btn-products" onclick="switchTab('products')" class="admin-tab-btn px-4 py-2.5 rounded-xl text-xs font-extrabold flex items-center gap-2 border transition-all">
            <i class="bi bi-boxes"></i> Products & Inventory
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-500/20 text-blue-300 border border-blue-500/30">{{ count($products) }}</span>
        </button>

        <button id="tab-btn-users" onclick="switchTab('users')" class="admin-tab-btn px-4 py-2.5 rounded-xl text-xs font-extrabold flex items-center gap-2 border transition-all">
            <i class="bi bi-people-fill"></i> Registered Users
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-purple-500/20 text-purple-300 border border-purple-500/30">{{ $totalUsers }}</span>
        </button>
    </div>

    <!-- TAB PANE 1: Customer Order Management -->
    <div id="tab-content-orders" class="tab-pane space-y-4">
        <div class="glass-panel border-gray-800 overflow-hidden">
            <div class="p-4 border-b border-gray-800 bg-gray-900/60 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="bi bi-receipt text-cyan-400"></i>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Customer Order Management</h3>
                </div>
                <span class="text-xs text-gray-400">{{ count($allOrders) }} Customer Orders Platform-wide</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-300">
                    <thead class="bg-gray-900/80 text-[10px] uppercase font-bold text-gray-400 border-b border-gray-800">
                        <tr>
                            <th class="p-4">Order #</th>
                            <th class="p-4">Customer Details</th>
                            <th class="p-4">Purchased Items</th>
                            <th class="p-4">Total Amount</th>
                            <th class="p-4">Fulfillment Status</th>
                            <th class="p-4 text-right">Update Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @forelse($allOrders as $order)
                            <tr class="hover:bg-gray-900/40">
                                <td class="p-4 font-mono font-bold text-cyan-400">
                                    <a href="{{ route('orders.show', $order->order_number) }}" target="_blank" class="hover:underline flex items-center gap-1">
                                        {{ $order->order_number }}
                                        <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                                    </a>
                                    <span class="text-[10px] text-gray-500 font-sans block mt-0.5">{{ $order->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="font-bold text-white block">{{ $order->customer_name }}</span>
                                    <span class="text-[11px] text-gray-400 block">{{ $order->customer_email }}</span>
                                    <span class="text-[10px] text-gray-500 font-mono">{{ $order->customer_phone }}</span>
                                    @if($order->user)
                                        <button type="button" onclick="switchTab('users'); inspectUserDetails({{ $order->user->id }})" class="inline-block mt-1 px-2 py-0.5 rounded bg-purple-950 text-purple-300 hover:text-white text-[9px] font-bold border border-purple-500/30 transition-colors">
                                            <i class="bi bi-person-fill"></i> User #{{ $order->user->id }}
                                        </button>
                                    @else
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded bg-gray-800 text-gray-400 text-[9px]">
                                            Guest Order
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="space-y-1 max-w-xs">
                                        @foreach($order->items as $item)
                                            <div class="text-[11px] text-gray-300 flex items-center justify-between gap-2 bg-gray-950/40 px-2 py-1 rounded border border-gray-800/60">
                                                <span class="truncate font-medium">{{ $item->product_name }}</span>
                                                <span class="font-bold text-cyan-400 whitespace-nowrap">&times;{{ $item->quantity }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4 font-bold text-white">
                                    ${{ number_format($order->total_amount, 2) }}
                                    <span class="text-[10px] text-gray-500 block uppercase font-mono">{{ $order->payment_method }}</span>
                                </td>
                                <td class="p-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-950 text-amber-400 border-amber-500/30',
                                            'processing' => 'bg-blue-950 text-blue-400 border-blue-500/30',
                                            'shipped' => 'bg-indigo-950 text-indigo-400 border-indigo-500/30',
                                            'delivered' => 'bg-emerald-950 text-emerald-400 border-emerald-500/30',
                                            'cancelled' => 'bg-rose-950 text-rose-400 border-rose-500/30',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase border {{ $statusClasses[$order->status] ?? 'bg-gray-800 text-gray-300' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="input-dark text-[11px] py-1 px-2 bg-gray-950 rounded-lg border-gray-800 text-gray-200">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        <button type="submit" class="px-2.5 py-1 bg-cyan-600 hover:bg-cyan-500 text-gray-950 rounded-lg text-[11px] font-extrabold transition-colors">
                                            Save
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">No customer orders found in database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB PANE 2: Catalog Products & Inventory Management -->
    <div id="tab-content-products" class="tab-pane space-y-4 hidden">
        <div class="glass-panel border-cyan-500/30 overflow-hidden relative shadow-2xl">
            <div class="p-5 border-b border-gray-800 bg-gray-900/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-sm font-bold flex-shrink-0">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Product Inventory & Stock Management</h3>
                        <p class="text-[11px] text-gray-400">Live hardware inventory, stock warnings, and SKU management</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400 font-bold bg-gray-950 px-3 py-1 rounded-lg border border-gray-800">
                        <i class="bi bi-box-seam text-cyan-400 mr-1"></i> {{ count($products) }} Active SKUs
                    </span>
                    <button onclick="document.getElementById('add-product-modal').classList.remove('hidden')" class="px-3.5 py-1.5 rounded-lg cyber-glow-btn text-xs font-bold flex items-center gap-1.5">
                        <i class="bi bi-plus-lg"></i> Add Product SKU
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-300">
                    <thead class="bg-gray-950 text-[10px] uppercase font-bold text-gray-400 border-b border-gray-800">
                        <tr>
                            <th class="p-4">Product Name & SKU</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Price</th>
                            <th class="p-4">Stock Status</th>
                            <th class="p-4">Badges</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @foreach($products as $prod)
                            <tr class="hover:bg-gray-900/50 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-950 border border-gray-800 overflow-hidden flex-shrink-0">
                                            <img src="{{ $prod->image }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ route('shop.show', $prod->slug) }}" target="_blank" class="font-bold text-white hover:text-cyan-300 block truncate transition-colors">
                                                {{ $prod->name }}
                                            </a>
                                            <span class="text-[10px] text-gray-500 font-mono block">{{ $prod->slug }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-cyan-400 font-semibold">{{ $prod->category->name ?? 'Uncategorized' }}</td>
                                <td class="p-4 font-bold text-white">
                                    ${{ number_format($prod->active_price, 2) }}
                                    @if($prod->sale_price)
                                        <span class="text-[10px] text-emerald-400 line-through block font-normal">${{ number_format($prod->price, 2) }}</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($prod->stock > 10)
                                        <span class="font-bold px-2.5 py-1 rounded-full text-[10px] uppercase inline-flex items-center gap-1 bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">
                                            <i class="bi bi-check-circle-fill text-[9px]"></i> {{ $prod->stock }} Units
                                        </span>
                                    @elseif($prod->stock > 0)
                                        <span class="font-bold px-2.5 py-1 rounded-full text-[10px] uppercase inline-flex items-center gap-1 bg-amber-950/80 text-amber-400 border border-amber-500/30 animate-pulse">
                                            <i class="bi bi-exclamation-triangle-fill text-[9px]"></i> {{ $prod->stock }} Low Stock
                                        </span>
                                    @else
                                        <span class="font-bold px-2.5 py-1 rounded-full text-[10px] uppercase inline-flex items-center gap-1 bg-rose-950/80 text-rose-400 border border-rose-500/30">
                                            <i class="bi bi-x-circle-fill text-[9px]"></i> Out of Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 space-x-1">
                                    @if($prod->is_featured)
                                        <span class="px-2 py-0.5 rounded bg-blue-950 text-cyan-400 text-[9px] font-bold border border-cyan-500/30">FEATURED</span>
                                    @endif
                                    @if($prod->is_trending)
                                        <span class="px-2 py-0.5 rounded bg-amber-950 text-amber-400 text-[9px] font-bold border border-amber-500/30">TRENDING</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <button type="button" onclick="editProduct({{ json_encode($prod) }})" class="px-2.5 py-1 bg-indigo-950 hover:bg-indigo-900 text-indigo-300 rounded-lg text-[11px] font-bold border border-indigo-500/30 transition-colors inline-flex items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $prod->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-950 hover:bg-rose-900 text-rose-300 rounded-lg text-[11px] font-bold border border-rose-500/30 transition-colors inline-flex items-center gap-1">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB PANE 3: Registered User Management Directory -->
    <div id="tab-content-users" class="tab-pane space-y-4 hidden">
        <div class="glass-panel border-purple-500/30 overflow-hidden relative shadow-2xl">
            <!-- User Table Header & Search/Filter Toolbar -->
            <div class="p-5 border-b border-gray-800 bg-gray-900/80 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-400 flex items-center justify-center text-base font-bold flex-shrink-0">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Registered Accounts & Directory</h3>
                            <p class="text-[11px] text-gray-400">Search customer profiles, modify administrative roles, inspect order history, and reset credentials</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="hidden md:flex items-center gap-2 text-xs text-gray-400 mr-2">
                            <span class="px-2.5 py-1 rounded-lg bg-gray-950 border border-gray-800 font-mono">
                                <strong class="text-white">{{ $customersCount }}</strong> Customers
                            </span>
                            <span class="px-2.5 py-1 rounded-lg bg-gray-950 border border-gray-800 font-mono">
                                <strong class="text-cyan-400">{{ $adminsCount }}</strong> Admins
                            </span>
                        </div>
                        <button onclick="document.getElementById('add-user-modal').classList.remove('hidden')" class="px-3.5 py-1.5 rounded-lg bg-purple-600/30 hover:bg-purple-600/40 text-purple-200 border border-purple-500/40 text-xs font-bold flex items-center gap-1.5 transition-all">
                            <i class="bi bi-person-plus-fill"></i> Add User
                        </button>
                    </div>
                </div>

                <!-- Live Search & Role Filter Form -->
                <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3 pt-2 border-t border-gray-800/80">
                    <input type="hidden" name="tab" value="users">
                    
                    <div class="relative flex-1 w-full">
                        <i class="bi bi-search absolute left-3 top-2.5 text-gray-500 text-xs"></i>
                        <input type="text" name="user_q" value="{{ request('user_q') }}" placeholder="Search by name, email, or phone number..." 
                            class="w-full bg-gray-950 border border-gray-800 rounded-xl py-2 pl-9 pr-3 text-xs text-gray-100 placeholder-gray-500 focus:outline-none focus:border-purple-500 transition-colors">
                    </div>

                    <div class="w-full sm:w-48">
                        <select name="user_role" onchange="this.form.submit()" class="w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-200 focus:outline-none focus:border-purple-500">
                            <option value="">All Roles ({{ $totalUsers }})</option>
                            <option value="customer" {{ request('user_role') === 'customer' ? 'selected' : '' }}>Customers Only ({{ $customersCount }})</option>
                            <option value="admin" {{ request('user_role') === 'admin' ? 'selected' : '' }}>Admins Only ({{ $adminsCount }})</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl text-xs transition-colors whitespace-nowrap">
                            Filter
                        </button>
                        @if(request('user_q') || request('user_role'))
                            <a href="{{ route('admin.dashboard', ['tab' => 'users']) }}" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-xl text-xs transition-colors whitespace-nowrap">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-300">
                    <thead class="bg-gray-950 text-[10px] uppercase font-bold text-gray-400 border-b border-gray-800">
                        <tr>
                            <th class="p-4">User Details</th>
                            <th class="p-4">Contact Info</th>
                            <th class="p-4">Access Role</th>
                            <th class="p-4">Order Activity</th>
                            <th class="p-4">Registered On</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-900/50 transition-colors {{ auth()->id() === $user->id ? 'bg-purple-950/20' : '' }}">
                                <!-- User Details & Avatar -->
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm uppercase flex-shrink-0 {{ $user->isAdmin() ? 'bg-gradient-to-tr from-cyan-600 to-indigo-600 text-white shadow-md shadow-cyan-500/20' : 'bg-gray-800 border border-gray-700 text-gray-200' }}">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-white text-xs truncate">{{ $user->name }}</span>
                                                @if(auth()->id() === $user->id)
                                                    <span class="px-1.5 py-0.2 rounded bg-purple-500/20 text-purple-300 text-[9px] font-mono font-bold border border-purple-500/40">YOU</span>
                                                @endif
                                            </div>
                                            <span class="text-[10px] text-gray-500 font-mono block">UID: #{{ $user->id }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact Info -->
                                <td class="p-4 space-y-0.5">
                                    <div class="flex items-center gap-1.5 text-gray-200">
                                        <i class="bi bi-envelope text-gray-500 text-[11px]"></i>
                                        <a href="mailto:{{ $user->email }}" class="hover:text-cyan-400 hover:underline transition-colors">{{ $user->email }}</a>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] text-gray-400 font-mono">
                                        <i class="bi bi-telephone text-gray-500 text-[10px]"></i>
                                        <span>{{ $user->phone ?? 'None registered' }}</span>
                                    </div>
                                </td>

                                <!-- Access Role & Quick Switch -->
                                <td class="p-4">
                                    @if(auth()->id() === $user->id || ($user->isAdmin() && $adminsCount <= 1))
                                        <!-- Protected role (self or last admin) -->
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-flex items-center gap-1.5 {{ $user->isAdmin() ? 'bg-cyan-950 text-cyan-300 border border-cyan-500/30' : 'bg-gray-800 text-gray-300' }}">
                                            <i class="bi bi-shield-check"></i> {{ strtoupper($user->role) }}
                                        </span>
                                    @else
                                        <!-- Role Toggle Form -->
                                        <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" onchange="this.form.submit()" class="input-dark text-[11px] py-1 px-2.5 bg-gray-950 rounded-lg border-gray-800 text-gray-200 font-bold focus:border-purple-500 transition-colors {{ $user->isAdmin() ? 'text-cyan-300 border-cyan-500/30' : 'text-gray-300' }}">
                                                <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>

                                <!-- Order Activity -->
                                <td class="p-4">
                                    <div class="font-bold text-white flex items-center gap-1">
                                        <i class="bi bi-bag-check text-cyan-400 text-xs"></i>
                                        <span>{{ $user->orders_count }} {{ Str::plural('order', $user->orders_count) }}</span>
                                    </div>
                                    <span class="text-[10px] text-emerald-400 font-mono block font-semibold">
                                        ${{ number_format($user->total_spent ?? 0, 2) }} total spend
                                    </span>
                                </td>

                                <!-- Registered On -->
                                <td class="p-4">
                                    <span class="text-white block">{{ $user->created_at->format('M d, Y') }}</span>
                                    <span class="text-[10px] text-gray-500 block">{{ $user->created_at->diffForHumans() }}</span>
                                </td>

                                <!-- Actions -->
                                <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                                    <!-- View/Inspect Activity -->
                                    <button type="button" onclick="inspectUserDetails({{ $user->id }})" title="Inspect User Order History" class="px-2.5 py-1 bg-cyan-950 hover:bg-cyan-900 text-cyan-300 rounded-lg text-[11px] font-bold border border-cyan-500/30 transition-colors inline-flex items-center gap-1">
                                        <i class="bi bi-eye"></i> Inspect
                                    </button>

                                    <!-- Edit Profile -->
                                    <button type="button" onclick="editUser({{ json_encode($user) }})" title="Edit User Profile" class="px-2.5 py-1 bg-indigo-950 hover:bg-indigo-900 text-indigo-300 rounded-lg text-[11px] font-bold border border-indigo-500/30 transition-colors inline-flex items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <!-- Reset Password -->
                                    <button type="button" onclick="resetPasswordModal({{ json_encode($user) }})" title="Reset User Password" class="px-2.5 py-1 bg-amber-950 hover:bg-amber-900 text-amber-300 rounded-lg text-[11px] font-bold border border-amber-500/30 transition-colors inline-flex items-center gap-1">
                                        <i class="bi bi-key-fill"></i> Reset Pass
                                    </button>

                                    <!-- Delete User -->
                                    @if(auth()->id() === $user->id)
                                        <button disabled title="You cannot delete your own active account" class="px-2.5 py-1 bg-gray-900 text-gray-600 rounded-lg text-[11px] font-bold cursor-not-allowed inline-flex items-center gap-1">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
                                    @elseif($user->isAdmin() && $adminsCount <= 1)
                                        <button disabled title="Cannot delete last remaining administrator" class="px-2.5 py-1 bg-gray-900 text-gray-600 rounded-lg text-[11px] font-bold cursor-not-allowed inline-flex items-center gap-1">
                                            <i class="bi bi-shield-slash"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete user \'{{ $user->name }}\'? Their past order transaction history will be safely preserved.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete User Account" class="px-2.5 py-1 bg-rose-950 hover:bg-rose-900 text-rose-300 rounded-lg text-[11px] font-bold border border-rose-500/30 transition-colors inline-flex items-center gap-1">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">
                                    <div class="max-w-sm mx-auto space-y-2">
                                        <i class="bi bi-person-x text-3xl text-gray-600 block"></i>
                                        <p class="text-sm font-semibold text-gray-400">No registered users matched your criteria.</p>
                                        @if(request('user_q') || request('user_role'))
                                            <a href="{{ route('admin.dashboard', ['tab' => 'users']) }}" class="inline-block mt-2 px-3 py-1 bg-purple-600/30 text-purple-300 hover:text-white rounded-lg text-xs font-bold border border-purple-500/30">
                                                Clear Search Filters
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ================= MODALS ================= -->

<!-- Modal: Add Product -->
<div id="add-product-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="glass-panel p-6 border-gray-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto space-y-4">
        <div class="flex items-center justify-between border-b border-gray-800 pb-3">
            <h3 class="text-base font-bold text-white">Add New Product to Catalog</h3>
            <button onclick="document.getElementById('add-product-modal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Product Title *</label>
                <input type="text" name="name" required placeholder="e.g. SE Neural Studio Microphone" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Category *</label>
                    <select name="category_id" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Tagline</label>
                    <input type="text" name="tagline" placeholder="Short highlights..." class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                </div>
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Description *</label>
                <textarea name="description" rows="3" required placeholder="Full product details..." class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Regular Price ($) *</label>
                    <input type="number" step="0.01" name="price" required placeholder="199.99" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                </div>
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Sale Price ($)</label>
                    <input type="number" step="0.01" name="sale_price" placeholder="169.99" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                </div>
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Stock Quantity *</label>
                    <input type="number" name="stock" value="25" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                </div>
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Image URL *</label>
                <input type="url" name="image" required value="https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=1000&q=80" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 font-mono">
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded bg-gray-900 border-gray-700 text-blue-600">
                    <span class="text-gray-300 font-semibold">Featured Product</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_trending" value="1" class="rounded bg-gray-900 border-gray-700 text-blue-600">
                    <span class="text-gray-300 font-semibold">Trending Product</span>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-800 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-product-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-lg">Cancel</button>
                <button type="submit" class="px-6 py-2 cyber-glow-btn font-bold rounded-lg">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Product -->
<div id="edit-product-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="glass-panel p-6 border-gray-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto space-y-4">
        <div class="flex items-center justify-between border-b border-gray-800 pb-3">
            <h3 class="text-base font-bold text-white">Edit Product Details & Stock</h3>
            <button onclick="document.getElementById('edit-product-modal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="edit-product-form" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Product Title *</label>
                <input type="text" name="name" id="edit_name" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Category *</label>
                    <select name="category_id" id="edit_category_id" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Tagline</label>
                    <input type="text" name="tagline" id="edit_tagline" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                </div>
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Description *</label>
                <textarea name="description" id="edit_description" rows="3" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Regular Price ($) *</label>
                    <input type="number" step="0.01" name="price" id="edit_price" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                </div>
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Sale Price ($)</label>
                    <input type="number" step="0.01" name="sale_price" id="edit_sale_price" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                </div>
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Stock Quantity *</label>
                    <input type="number" name="stock" id="edit_stock" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                </div>
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Image URL *</label>
                <input type="url" name="image" id="edit_image" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 font-mono">
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" id="edit_is_featured" value="1" class="rounded bg-gray-900 border-gray-700 text-blue-600">
                    <span class="text-gray-300 font-semibold">Featured Product</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_trending" id="edit_is_trending" value="1" class="rounded bg-gray-900 border-gray-700 text-blue-600">
                    <span class="text-gray-300 font-semibold">Trending Product</span>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-800 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('edit-product-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-lg">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg">Update Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add New User -->
<div id="add-user-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="glass-panel p-6 border-purple-500/40 w-full max-w-lg max-h-[90vh] overflow-y-auto space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-800 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center text-sm font-bold">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h3 class="text-base font-bold text-white">Create Registered Account</h3>
            </div>
            <button onclick="document.getElementById('add-user-modal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Full Name *</label>
                <input type="text" name="name" required placeholder="e.g. Sarah Connor" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 focus:border-purple-500">
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Email Address *</label>
                <input type="email" name="email" required placeholder="sarah@example.com" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 focus:border-purple-500">
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Phone Number (Optional)</label>
                <input type="text" name="phone" placeholder="+1 (555) 019-2834" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 focus:border-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Role Permission *</label>
                    <select name="role" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 focus:border-purple-500">
                        <option value="customer" selected>Customer</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="text-gray-300 font-bold uppercase block mb-1">Initial Password *</label>
                    <input type="password" name="password" required minlength="8" placeholder="Min 8 characters" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 focus:border-purple-500">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-800 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('add-user-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-lg">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-lg shadow-lg shadow-purple-500/20">Create User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit User -->
<div id="edit-user-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="glass-panel p-6 border-indigo-500/40 w-full max-w-lg max-h-[90vh] overflow-y-auto space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-800 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm font-bold">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h3 class="text-base font-bold text-white">Edit User Account</h3>
            </div>
            <button onclick="document.getElementById('edit-user-modal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="edit-user-form" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Full Name *</label>
                <input type="text" name="name" id="edit_user_name" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Email Address *</label>
                <input type="email" name="email" id="edit_user_email" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Phone Number</label>
                <input type="text" name="phone" id="edit_user_phone" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Role Permission *</label>
                <select name="role" id="edit_user_role" required class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100">
                    <option value="customer">Customer</option>
                    <option value="admin">Administrator</option>
                </select>
                <p id="edit_user_role_warning" class="text-[10px] text-amber-400 mt-1 hidden"></p>
            </div>

            <div class="pt-4 border-t border-gray-800 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('edit-user-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-lg">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reset User Password -->
<div id="reset-user-password-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="glass-panel p-6 border-amber-500/40 w-full max-w-md max-h-[90vh] overflow-y-auto space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-800 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm font-bold">
                    <i class="bi bi-key-fill"></i>
                </div>
                <h3 class="text-base font-bold text-white">Reset Account Password</h3>
            </div>
            <button onclick="document.getElementById('reset-user-password-modal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="reset-password-form" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PATCH')

            <div class="p-3 rounded-xl bg-amber-950/40 border border-amber-500/30 text-amber-300 text-[11px]">
                Resetting password for: <strong id="reset_password_username" class="text-white block font-mono text-xs mt-0.5"></strong>
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">New Password *</label>
                <input type="password" name="password" required minlength="8" placeholder="Min 8 characters" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 focus:border-amber-500">
            </div>

            <div>
                <label class="text-gray-300 font-bold uppercase block mb-1">Confirm New Password *</label>
                <input type="password" name="password_confirmation" required minlength="8" placeholder="Repeat new password" class="input-dark w-full bg-gray-950 border border-gray-800 rounded-xl py-2 px-3 text-xs text-gray-100 focus:border-amber-500">
            </div>

            <div class="pt-4 border-t border-gray-800 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('reset-user-password-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-lg">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-amber-600 hover:bg-amber-500 text-gray-950 font-bold rounded-lg">Update Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Inspect User Order History & Activity -->
<div id="inspect-user-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="glass-panel p-6 border-cyan-500/40 w-full max-w-3xl max-h-[90vh] overflow-y-auto space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-800 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-base font-bold">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">User Activity & Order Ledger</h3>
                    <p class="text-[11px] text-gray-400" id="inspect_user_subtitle">Loading account profile...</p>
                </div>
            </div>
            <button onclick="document.getElementById('inspect-user-modal').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Inspect Content Container -->
        <div id="inspect_content_container" class="space-y-4">
            <!-- Loading Spinner State -->
            <div id="inspect_loader" class="py-12 text-center text-cyan-400 space-y-2">
                <i class="bi bi-arrow-repeat animate-spin text-3xl inline-block"></i>
                <p class="text-xs text-gray-400">Fetching user ledger from database...</p>
            </div>

            <!-- Loaded State -->
            <div id="inspect_body" class="space-y-4 hidden">
                <!-- User Summary Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 rounded-xl bg-gray-950 border border-gray-800 space-y-0.5">
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Account Role</span>
                        <div id="inspect_badge_role" class="text-xs font-bold uppercase text-white font-mono"></div>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-950 border border-gray-800 space-y-0.5">
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Total Orders</span>
                        <div id="inspect_orders_count" class="text-xs font-black text-cyan-400">0</div>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-950 border border-gray-800 space-y-0.5">
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Lifetime Spend</span>
                        <div id="inspect_total_spent" class="text-xs font-black text-emerald-400">$0.00</div>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-950 border border-gray-800 space-y-0.5">
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Member Since</span>
                        <div id="inspect_created_at" class="text-[11px] font-bold text-gray-300"></div>
                    </div>
                </div>

                <!-- Recent Orders Table -->
                <div class="rounded-xl border border-gray-800 overflow-hidden bg-gray-950/60">
                    <div class="p-3 border-b border-gray-800 bg-gray-900/40 flex items-center justify-between">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                            <i class="bi bi-clock-history text-cyan-400"></i> Recent Order Records
                        </h4>
                        <span class="text-[10px] text-gray-500 font-mono">Last 10 orders</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-300">
                            <thead class="bg-gray-950 text-[10px] uppercase font-bold text-gray-500 border-b border-gray-800">
                                <tr>
                                    <th class="p-3">Order #</th>
                                    <th class="p-3">Date</th>
                                    <th class="p-3">Items Summary</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="inspect_orders_table_body" class="divide-y divide-gray-800/60">
                                <!-- Populated dynamically by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-3 border-t border-gray-800 flex justify-end">
            <button type="button" onclick="document.getElementById('inspect-user-modal').classList.add('hidden')" class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 font-bold rounded-lg text-xs">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    // Tab Management
    const tabPanes = {
        'orders': document.getElementById('tab-content-orders'),
        'products': document.getElementById('tab-content-products'),
        'users': document.getElementById('tab-content-users')
    };

    const tabButtons = {
        'orders': document.getElementById('tab-btn-orders'),
        'products': document.getElementById('tab-btn-products'),
        'users': document.getElementById('tab-btn-users')
    };

    function switchTab(activeTab) {
        if (!tabPanes[activeTab]) activeTab = 'orders';

        // Toggle pane visibility
        Object.keys(tabPanes).forEach(tab => {
            if (tab === activeTab) {
                tabPanes[tab].classList.remove('hidden');
            } else {
                tabPanes[tab].classList.add('hidden');
            }
        });

        // Toggle button styling
        Object.keys(tabButtons).forEach(tab => {
            const btn = tabButtons[tab];
            if (tab === activeTab) {
                btn.className = 'admin-tab-btn px-4 py-2.5 rounded-xl text-xs font-extrabold flex items-center gap-2 border transition-all bg-cyan-500/15 border-cyan-500/40 text-cyan-300 shadow-md shadow-cyan-500/10';
            } else {
                btn.className = 'admin-tab-btn px-4 py-2.5 rounded-xl text-xs font-extrabold flex items-center gap-2 border transition-all bg-gray-900 border-gray-800 text-gray-400 hover:text-white';
            }
        });

        // Update URL hash without reload
        if (history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.set('tab', activeTab);
            history.replaceState(null, '', url);
        }
    }

    // Auto-select tab based on URL query or hash
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        const hasUserSearch = urlParams.has('user_q') || urlParams.has('user_role');

        if (hasUserSearch || tabParam === 'users' || window.location.hash === '#users') {
            switchTab('users');
        } else if (tabParam === 'products' || window.location.hash === '#products') {
            switchTab('products');
        } else {
            switchTab('orders');
        }
    });

    // Product Modals
    function editProduct(product) {
        document.getElementById('edit-product-form').action = `/admin/products/${product.id}`;
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_category_id').value = product.category_id;
        document.getElementById('edit_tagline').value = product.tagline || '';
        document.getElementById('edit_description').value = product.description;
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_sale_price').value = product.sale_price || '';
        document.getElementById('edit_stock').value = product.stock;
        document.getElementById('edit_image').value = product.image;
        document.getElementById('edit_is_featured').checked = product.is_featured ? true : false;
        document.getElementById('edit_is_trending').checked = product.is_trending ? true : false;

        document.getElementById('edit-product-modal').classList.remove('hidden');
    }

    // User Modals
    const currentUserId = {{ auth()->id() }};

    function editUser(user) {
        document.getElementById('edit-user-form').action = `/admin/users/${user.id}`;
        document.getElementById('edit_user_name').value = user.name;
        document.getElementById('edit_user_email').value = user.email;
        document.getElementById('edit_user_phone').value = user.phone || '';
        
        const roleSelect = document.getElementById('edit_user_role');
        roleSelect.value = user.role;

        const warningEl = document.getElementById('edit_user_role_warning');
        if (user.id === currentUserId) {
            warningEl.textContent = 'Note: You cannot remove admin role from your own active account.';
            warningEl.classList.remove('hidden');
        } else {
            warningEl.classList.add('hidden');
        }

        document.getElementById('edit-user-modal').classList.remove('hidden');
    }

    function resetPasswordModal(user) {
        document.getElementById('reset-password-form').action = `/admin/users/${user.id}/password`;
        document.getElementById('reset_password_username').textContent = `${user.name} (${user.email})`;
        document.getElementById('reset-user-password-modal').classList.remove('hidden');
    }

    function inspectUserDetails(userId) {
        const modal = document.getElementById('inspect-user-modal');
        const loader = document.getElementById('inspect_loader');
        const body = document.getElementById('inspect_body');
        const subtitle = document.getElementById('inspect_user_subtitle');
        const tableBody = document.getElementById('inspect_orders_table_body');

        modal.classList.remove('hidden');
        loader.classList.remove('hidden');
        body.classList.add('hidden');
        subtitle.textContent = `Fetching account details for User #${userId}...`;

        fetch(`/admin/users/${userId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Failed to load user details.');
            return res.json();
        })
        .then(data => {
            const user = data.user;
            const orders = data.orders || [];

            subtitle.textContent = `${user.name} &bull; ${user.email} ${user.phone ? '&bull; ' + user.phone : ''}`;
            document.getElementById('inspect_badge_role').textContent = user.role;
            document.getElementById('inspect_orders_count').textContent = user.orders_count || 0;
            document.getElementById('inspect_total_spent').textContent = '$' + (parseFloat(user.total_spent || 0).toFixed(2));
            
            const joinDate = new Date(user.created_at);
            document.getElementById('inspect_created_at').textContent = joinDate.toLocaleDateString(undefined, {
                year: 'numeric', month: 'short', day: 'numeric'
            });

            // Populate orders table
            if (orders.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-gray-500">No orders placed by this user yet.</td></tr>`;
            } else {
                tableBody.innerHTML = orders.map(ord => {
                    const ordDate = new Date(ord.created_at).toLocaleDateString(undefined, {
                        month: 'short', day: 'numeric', year: 'numeric'
                    });
                    const itemsSummary = (ord.items || []).map(i => `${i.product_name} &times; ${i.quantity}`).join(', ') || 'N/A';
                    
                    const statusColors = {
                        'pending': 'text-amber-400 bg-amber-950/60 border-amber-500/30',
                        'processing': 'text-blue-400 bg-blue-950/60 border-blue-500/30',
                        'shipped': 'text-indigo-400 bg-indigo-950/60 border-indigo-500/30',
                        'delivered': 'text-emerald-400 bg-emerald-950/60 border-emerald-500/30',
                        'cancelled': 'text-rose-400 bg-rose-950/60 border-rose-500/30'
                    };
                    const statusClass = statusColors[ord.status] || 'text-gray-300 bg-gray-900 border-gray-800';

                    return `
                        <tr class="hover:bg-gray-900/40">
                            <td class="p-3 font-mono font-bold text-cyan-400">
                                <a href="/order/${ord.order_number}" target="_blank" class="hover:underline flex items-center gap-1">
                                    ${ord.order_number}
                                    <i class="bi bi-box-arrow-up-right text-[10px]"></i>
                                </a>
                            </td>
                            <td class="p-3 text-gray-400 font-mono text-[11px]">${ordDate}</td>
                            <td class="p-3 text-gray-300 max-w-xs truncate" title="${itemsSummary}">${itemsSummary}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border ${statusClass}">${ord.status}</span>
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-white">$${parseFloat(ord.total_amount).toFixed(2)}</td>
                        </tr>
                    `;
                }).join('');
            }

            loader.classList.add('hidden');
            body.classList.remove('hidden');
        })
        .catch(err => {
            subtitle.textContent = 'Error loading user data.';
            tableBody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-rose-400">${err.message}</td></tr>`;
            loader.classList.add('hidden');
            body.classList.remove('hidden');
        });
    }
</script>
@endsection
