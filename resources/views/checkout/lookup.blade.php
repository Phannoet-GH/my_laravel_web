@extends('layouts.app')

@section('title', 'Track Your Order — SE Shop')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-16 px-4 relative overflow-hidden">

    {{-- Background glow --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[500px] h-[300px] bg-blue-600/8 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-lg relative z-10 space-y-6">

        {{-- Header --}}
        <div class="text-center space-y-4">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-950 via-indigo-950 to-purple-950 border border-blue-500/40 text-cyan-400 text-3xl shadow-2xl shadow-blue-500/20 mx-auto mb-3">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="space-y-2">
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Track Your Order</h1>
                <p class="text-sm text-gray-400 max-w-sm mx-auto leading-relaxed mt-2">Enter your order details below to view real-time delivery status</p>
            </div>
        </div>

        {{-- Errors / Info --}}
        @if ($errors->any())
            <div class="alert alert-error animate-slide-in-up">
                <i class="alert-icon bi bi-exclamation-circle-fill text-lg mr-2"></i>
                <div class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="text-xs">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Lookup Card --}}
        <div class="glass-panel p-8 space-y-6">

            <form action="{{ route('orders.lookup.post') }}" method="POST" id="lookupForm" class="space-y-6">
                @csrf

                <div class="form-group space-y-2">
                    <label for="order_number" class="form-label font-semibold text-gray-300">Order Number</label>
                    <div class="input-icon-group">
                        <span class="input-icon-left">
                            <i class="bi bi-hash text-cyan-400 text-lg"></i>
                        </span>
                        <input id="order_number" name="order_number" type="text"
                            value="{{ old('order_number') }}"
                            placeholder="SE-ORD-XXXXXX"
                            required
                            style="padding-left: 3.5rem !important;"
                            class="input-dark py-3.5 font-mono tracking-wider uppercase {{ $errors->has('order_number') ? 'border-rose-500/70' : '' }}"
                            oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-2 flex items-center gap-1.5">
                        <i class="bi bi-info-circle text-gray-400 text-xs"></i> <span>Format: SE-ORD-XXXXXX (from your confirmation email)</span>
                    </p>
                </div>

                <div class="form-group space-y-2">
                    <label for="customer_email" class="form-label font-semibold text-gray-300">Email Address</label>
                    <div class="input-icon-group">
                        <span class="input-icon-left">
                            <i class="bi bi-envelope text-cyan-400 text-lg"></i>
                        </span>
                        <input id="customer_email" name="customer_email" type="email"
                            value="{{ old('customer_email') }}"
                            placeholder="Email used when placing the order"
                            required
                            style="padding-left: 3.5rem !important;"
                            class="input-dark py-3.5 {{ $errors->has('customer_email') ? 'border-rose-500/70' : '' }}">
                    </div>
                </div>

                <button type="submit" id="trackBtn" class="btn btn-primary btn-full btn-lg inline-flex items-center justify-center gap-3.5 group py-3.5 px-6 shadow-xl shadow-cyan-500/20">
                    <i class="bi bi-geo-alt-fill text-lg"></i>
                    <span id="trackBtnText" class="font-bold tracking-wide">Track My Order</span>
                    <i class="bi bi-arrow-right text-lg group-hover:translate-x-1.5 transition-transform" id="trackBtnIcon"></i>
                    <span id="trackBtnSpinner" class="spinner hidden"></span>
                </button>
            </form>

            <div class="border-t border-gray-800/60 pt-5 space-y-3">
                <p class="text-xs font-semibold text-gray-400 text-center">Quick Links</p>
                <div class="grid grid-cols-2 gap-2.5">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm gap-1.5 justify-center">
                            <i class="bi bi-person-circle text-cyan-400"></i> My Orders
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm gap-1.5 justify-center">
                            <i class="bi bi-box-arrow-in-right text-cyan-400"></i> Sign In
                        </a>
                    @endauth
                    <a href="{{ route('shop.index') }}" class="btn btn-ghost btn-sm gap-1.5 justify-center">
                        <i class="bi bi-bag text-indigo-400"></i> Shop Now
                    </a>
                </div>
            </div>
        </div>

        {{-- Help text --}}
        <p class="text-center text-xs text-gray-500">
            Can't find your order?
            <a href="{{ route('home') }}" class="text-cyan-400 hover:underline font-semibold">Contact Support</a>
        </p>
    </div>
</div>

<script>
    document.getElementById('lookupForm')?.addEventListener('submit', () => {
        document.getElementById('trackBtnText').textContent = 'Looking up...';
        document.getElementById('trackBtnIcon').classList.add('hidden');
        document.getElementById('trackBtnSpinner').classList.remove('hidden');
        document.getElementById('trackBtn').disabled = true;
    });
</script>
@endsection
