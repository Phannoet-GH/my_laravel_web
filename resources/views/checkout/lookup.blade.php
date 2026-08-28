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
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-950 to-indigo-950 border border-blue-500/30 text-cyan-400 text-2xl shadow-xl">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white">Track Your Order</h1>
                <p class="text-sm text-gray-400 mt-1">Enter your order details to see real-time delivery status</p>
            </div>
        </div>

        {{-- Errors / Info --}}
        @if ($errors->any())
            <div class="alert alert-error animate-slide-in-up">
                <i class="alert-icon bi bi-exclamation-circle-fill"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Lookup Card --}}
        <div class="glass-panel p-8 space-y-6">

            <form action="{{ route('orders.lookup.post') }}" method="POST" id="lookupForm" class="space-y-5">
                @csrf

                <div class="form-group">
                    <label for="order_number" class="form-label">Order Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 text-sm">
                            <i class="bi bi-hash"></i>
                        </span>
                        <input id="order_number" name="order_number" type="text"
                            value="{{ old('order_number') }}"
                            placeholder="SE-ORD-XXXXXX"
                            required
                            class="input-dark pl-10 font-mono tracking-wider uppercase {{ $errors->has('order_number') ? 'border-rose-500/70' : '' }}"
                            oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <p class="text-[10px] text-gray-500 mt-1">Format: SE-ORD-XXXXXX (from your confirmation email)</p>
                </div>

                <div class="form-group">
                    <label for="customer_email" class="form-label">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500 text-sm">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="customer_email" name="customer_email" type="email"
                            value="{{ old('customer_email') }}"
                            placeholder="Email used when placing the order"
                            required
                            class="input-dark pl-10 {{ $errors->has('customer_email') ? 'border-rose-500/70' : '' }}">
                    </div>
                </div>

                <button type="submit" id="trackBtn" class="btn btn-primary btn-full btn-lg gap-2 group">
                    <span id="trackBtnText">Track My Order</span>
                    <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform" id="trackBtnIcon"></i>
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
