@extends('layouts.app')

@section('title', 'My Account — SE Shop')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    {{-- ── WELCOME HEADER ── --}}
    <div class="relative rounded-2xl overflow-hidden p-8 border border-blue-500/20"
        style="background: linear-gradient(135deg, rgba(30,58,138,0.6) 0%, rgba(49,46,129,0.5) 50%, rgba(88,28,135,0.5) 100%);">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20viewBox%3D%220%200%2040%2040%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Ccircle%20cx%3D%2220%22%20cy%3D%2220%22%20r%3D%221%22%20fill%3D%22rgba(255%2C255%2C255%2C0.04)%22/%3E%3C/svg%3E')] opacity-60 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                {{-- Avatar --}}
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-2xl font-black text-white shadow-xl shadow-blue-500/30 flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="section-eyebrow text-cyan-400 border-cyan-500/30 bg-cyan-500/8 mb-2">
                        <i class="bi bi-person-check-fill"></i> Customer Portal
                    </div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">
                        Welcome back, {{ $user->name }}!
                    </h1>
                    <p class="text-sm text-blue-200/70 mt-0.5">{{ $user->email }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm bg-indigo-600 hover:bg-indigo-500 text-white border-0 gap-2">
                        <i class="bi bi-speedometer2"></i> Admin Panel
                    </a>
                @endif
                <a href="{{ route('shop.index') }}" class="btn btn-cyan btn-sm gap-2">
                    <i class="bi bi-bag-plus"></i> Shop Now
                </a>
            </div>
        </div>
    </div>

    {{-- ── STAT METRIC CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-metric-card">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-600/15 border border-blue-600/30 text-blue-400 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-bag-check"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-white leading-none">{{ $totalOrders }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Total Orders</div>
                </div>
            </div>
        </div>

        <div class="stat-metric-card">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-cyan-600/15 border border-cyan-600/30 text-cyan-400 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-white leading-none">${{ number_format($totalSpent, 0) }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Total Spent</div>
                </div>
            </div>
        </div>

        <div class="stat-metric-card">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/15 border border-indigo-600/30 text-indigo-400 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-white leading-none">{{ $activeOrders }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Active Orders</div>
                </div>
            </div>
        </div>

        <div class="stat-metric-card">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/15 border border-emerald-600/30 text-emerald-400 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-white leading-none capitalize">{{ $user->role }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Account Type</div>
                </div>
            </div>
        </div>
    </div>


    {{-- ── MAIN GRID ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── ORDER HISTORY (2 cols) ── --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-white flex items-center gap-2">
                    <i class="bi bi-box-seam text-cyan-400"></i> Order History
                </h2>
                <a href="{{ route('orders.lookup') }}" class="text-xs text-gray-400 hover:text-cyan-400 inline-flex items-center gap-2 transition-colors">
                    <i class="bi bi-geo-alt text-cyan-400"></i>
                    <span>Track Order</span>
                </a>
            </div>

            @if($orders->isEmpty())
                <div class="glass-panel p-16 text-center space-y-4">
                    <div class="empty-state-icon mx-auto"><i class="bi bi-bag-x"></i></div>
                    <h3 class="text-lg font-bold text-white">No Orders Yet</h3>
                    <p class="text-sm text-gray-400">Explore high-performance laptops, keyboards, and developer gear.</p>
                    <a href="{{ route('shop.index') }}" class="btn btn-primary inline-flex gap-2">
                        Browse Catalog <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($orders as $order)
                    <div class="stat-metric-card p-5 space-y-4 hover:border-cyan-500/40 transition-all group">

                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            {{-- Order number --}}
                            <div class="space-y-0.5">
                                <p class="text-[10px] text-gray-500 font-mono uppercase tracking-wider">Order #</p>
                                <a href="{{ route('orders.show', $order->order_number) }}"
                                    class="font-mono font-black text-sm text-white hover:text-cyan-400 transition-colors">
                                    {{ $order->order_number }}
                                </a>
                            </div>

                            {{-- Date --}}
                            <div class="space-y-0.5 text-right">
                                <p class="text-[10px] text-gray-500 font-mono uppercase tracking-wider">Date</p>
                                <p class="text-xs font-semibold text-gray-300">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>

                            {{-- Status badge --}}
                            <span class="badge {{ match(strtolower($order->status)) {
                                'pending'    => 'badge-amber',
                                'processing' => 'badge-blue',
                                'shipped'    => 'badge-indigo',
                                'delivered'  => 'badge-emerald',
                                'cancelled'  => 'badge-rose',
                                default      => 'badge-cyan'
                            } }}">
                                {{ ucfirst($order->status) }}
                            </span>

                            {{-- Total --}}
                            <div class="text-right">
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Total</p>
                                <p class="text-sm font-black text-cyan-400">${{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>

                        {{-- Items mini list --}}
                        @if($order->items->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach($order->items->take(3) as $item)
                                <span class="text-[10px] font-semibold text-gray-400 bg-gray-900/60 border border-gray-800 px-2.5 py-1 rounded-md">
                                    {{ Str::limit($item->product_name, 22) }} ×{{ $item->quantity }}
                                </span>
                            @endforeach
                            @if($order->items->count() > 3)
                                <span class="text-[10px] text-gray-500 px-2 py-1">+{{ $order->items->count() - 3 }} more</span>
                            @endif
                        </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <a href="{{ route('orders.show', $order->order_number) }}"
                                class="btn btn-ghost btn-sm gap-1.5">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank"
                                class="btn btn-ghost btn-sm gap-1.5">
                                <i class="bi bi-file-earmark-pdf"></i> Invoice
                            </a>
                            @if($order->isCancelable())
                                <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST"
                                    onsubmit="return confirm('Cancel order {{ $order->order_number }}?');"
                                    class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm gap-1.5">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($orders->hasPages())
                    <div class="flex justify-center mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- ── ACCOUNT SETTINGS (1 col) ── --}}
        <div class="space-y-5">

            {{-- Tab selector --}}
            <div class="flex gap-1 p-1 bg-gray-900/80 rounded-xl border border-gray-800">
                <button type="button" onclick="switchTab('profile')" id="tab-profile"
                    class="tab-btn flex-1 py-2 px-3 text-xs font-bold rounded-lg transition-all active">
                    <i class="bi bi-person mr-1.5"></i> Profile
                </button>
                <button type="button" onclick="switchTab('security')" id="tab-security"
                    class="tab-btn flex-1 py-2 px-3 text-xs font-bold rounded-lg transition-all">
                    <i class="bi bi-shield-lock mr-1.5"></i> Security
                </button>
            </div>

            {{-- ── PROFILE TAB ── --}}
            <div id="panel-profile" class="glass-panel p-6 space-y-5">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="bi bi-person-circle text-cyan-400"></i> Profile Information
                </h3>

                @if(session('success') && session('tab') !== 'security')
                    <div class="alert alert-success text-xs">
                        <i class="alert-icon bi bi-check-circle-fill"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Display Name</label>
                        <div class="input-icon-group">
                            <span class="input-icon-left">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="input-dark {{ $errors->has('name') ? 'border-rose-500/70' : '' }}">
                        </div>
                        @error('name') <p class="text-[10px] text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-icon-group">
                            <span class="input-icon-left">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="input-dark {{ $errors->has('email') ? 'border-rose-500/70' : '' }}">
                        </div>
                        @error('email') <p class="text-[10px] text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Account Role</label>
                        <div class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-gray-900/60 border border-gray-800">
                            <i class="bi bi-shield-check text-sm {{ $user->isAdmin() ? 'text-indigo-400' : 'text-cyan-400' }}"></i>
                            <span class="text-xs font-bold text-white capitalize">{{ $user->role }}</span>
                            <span class="ml-auto badge {{ $user->isAdmin() ? 'badge-indigo' : 'badge-cyan' }} text-[9px]">
                                {{ $user->isAdmin() ? 'Admin' : 'Customer' }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full btn-sm gap-2">
                        <i class="bi bi-check-lg"></i> Save Profile
                    </button>
                </form>
            </div>

            {{-- ── SECURITY TAB ── --}}
            <div id="panel-security" class="glass-panel p-6 space-y-5 hidden">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="bi bi-shield-lock text-indigo-400"></i> Change Password
                </h3>

                @if(session('success') && session('tab') === 'security')
                    <div class="alert alert-success text-xs">
                        <i class="alert-icon bi bi-check-circle-fill"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <div class="input-icon-group">
                            <span class="input-icon-left">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" name="current_password" required
                                class="input-dark {{ $errors->has('current_password') ? 'border-rose-500/70' : '' }}">
                        </div>
                        @error('current_password') <p class="text-[10px] text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <div class="input-icon-group">
                            <span class="input-icon-left">
                                <i class="bi bi-key"></i>
                            </span>
                            <input type="password" name="new_password" required minlength="8"
                                class="input-dark {{ $errors->has('new_password') ? 'border-rose-500/70' : '' }}">
                        </div>
                        @error('new_password') <p class="text-[10px] text-rose-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-icon-group">
                            <span class="input-icon-left">
                                <i class="bi bi-shield-check"></i>
                            </span>
                            <input type="password" name="new_password_confirmation" required minlength="8"
                                class="input-dark">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-full btn-sm gap-2 bg-indigo-600 hover:bg-indigo-500 text-white border-0">
                        <i class="bi bi-key-fill"></i> Update Password
                    </button>
                </form>
            </div>

            {{-- Quick Links --}}
            <div class="glass-panel p-4 space-y-2">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3">Quick Links</p>
                <a href="{{ route('shop.index') }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-800/60 text-gray-400 hover:text-white transition-all text-xs font-semibold group">
                    <i class="bi bi-bag text-cyan-400"></i> Browse Catalog
                    <i class="bi bi-chevron-right text-[10px] ml-auto opacity-0 group-hover:opacity-100"></i>
                </a>
                <a href="{{ route('orders.lookup') }}" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-800/60 text-gray-400 hover:text-white transition-all text-xs font-semibold group">
                    <i class="bi bi-search text-indigo-400"></i> Track an Order
                    <i class="bi bi-chevron-right text-[10px] ml-auto opacity-0 group-hover:opacity-100"></i>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-rose-950/50 text-gray-400 hover:text-rose-400 transition-all text-xs font-semibold w-full text-left group">
                        <i class="bi bi-box-arrow-right text-rose-400"></i> Sign Out
                        <i class="bi bi-chevron-right text-[10px] ml-auto opacity-0 group-hover:opacity-100"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

<script>
    function switchTab(tab) {
        // Panels
        document.getElementById('panel-profile').classList.toggle('hidden', tab !== 'profile');
        document.getElementById('panel-security').classList.toggle('hidden', tab !== 'security');

        // Buttons
        document.getElementById('tab-profile').classList.toggle('active', tab === 'profile');
        document.getElementById('tab-security').classList.toggle('active', tab === 'security');
    }

    // Auto-switch tab if errors on security or session tab
    @if($errors->has('current_password') || $errors->has('new_password') || session('tab') === 'security')
        switchTab('security');
    @endif
</script>

<style>
    .tab-btn { color: #6b7280; }
    .tab-btn:hover { color: #e2e8f0; background: rgba(255,255,255,0.04); }
    .tab-btn.active { background: rgba(59,130,246,0.12); color: #93c5fd; border: 1px solid rgba(59,130,246,0.2); }
</style>
@endsection
