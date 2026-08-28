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
            <p class="text-xs text-gray-400 mt-1">Manage merchant products, live inventory, and order fulfillment</p>
        </div>

        <button onclick="document.getElementById('add-product-modal').classList.remove('hidden')" class="px-5 py-3 rounded-xl cyber-glow-btn text-xs font-bold flex items-center justify-center gap-2 shadow-lg shadow-cyan-500/20">
            <i class="bi bi-plus-lg text-sm"></i> Add New Product
        </button>
    </div>


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
        <div class="stat-metric-card space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Orders</span>
                <div class="w-9 h-9 rounded-xl bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-base font-bold flex-shrink-0">
                    <i class="bi bi-cart-check"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-cyan-400">{{ $ordersCount }}</div>
            <span class="text-[10px] text-gray-400">Processed orders</span>
        </div>

        <!-- Stat 3: Registered Users -->
        <div class="stat-metric-card space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Registered Users</span>
                <div class="w-9 h-9 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-400 flex items-center justify-center text-base font-bold flex-shrink-0">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-purple-400">{{ $totalUsers }}</div>
            <span class="text-[10px] text-purple-300 font-semibold">Active accounts</span>
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
        </div>
    @endif

    <!-- Section 1: Customer Order Management -->
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
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded bg-purple-950 text-purple-300 text-[9px] font-bold border border-purple-500/30">
                                        User ID: #{{ $order->user->id }}
                                    </span>
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

    <!-- Section 2: Catalog Products & Inventory Management (CRUD Box) -->
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

<script>
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
</script>
@endsection
