@extends('layouts.app')

@section('title', 'Order Confirmed — ' . $order->order_number . ' | SE Shop')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">

    {{-- ── SUCCESS BANNER ── --}}
    <div class="glass-panel p-10 text-center space-y-5 relative overflow-hidden border-emerald-500/30 animate-scale-in">
        {{-- Background glow --}}
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/5 to-cyan-600/5 pointer-events-none"></div>

        {{-- Animated checkmark --}}
        <div class="relative inline-flex">
            <div class="w-20 h-20 rounded-full bg-emerald-500/15 border-2 border-emerald-500/40 flex items-center justify-center text-emerald-400 text-4xl animate-scale-in shadow-xl shadow-emerald-500/20">
                <i class="bi bi-check-lg"></i>
            </div>
            <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-cyan-400 flex items-center justify-center text-gray-950 text-xs font-black shadow-lg shadow-cyan-400/50 animate-bounce">✓</span>
        </div>

        <div>
            <span class="section-eyebrow text-emerald-400 border-emerald-500/30 bg-emerald-500/8">
                <i class="bi bi-bag-check-fill"></i> Order Successful
            </span>
            <h1 class="text-3xl font-extrabold text-white mt-3">Thank You For Your Order!</h1>
            <p class="text-sm text-gray-300 mt-2">
                A full receipt has been sent to
                <strong class="text-white">{{ $order->customer_email }}</strong>
            </p>
        </div>

        <div class="inline-flex items-center gap-3 px-6 py-3 rounded-xl bg-gray-900/60 border border-gray-800">
            <span class="text-xs text-gray-400">Order Reference</span>
            <span class="font-mono font-black text-cyan-400 text-sm tracking-wider">{{ $order->order_number }}</span>
            <button onclick="navigator.clipboard.writeText('{{ $order->order_number }}').then(()=>this.textContent='✓')"
                class="text-xs text-gray-500 hover:text-cyan-400 transition-colors font-mono ml-1">⎘</button>
        </div>
    </div>

    {{-- ── ORDER STATUS TIMELINE ── --}}
    <div class="glass-panel p-6 space-y-5">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-map text-cyan-400"></i> Fulfillment Timeline
            </h3>
            @if($order->tracking_code)
                <span class="mono-tag">{{ $order->tracking_code }}</span>
            @endif
        </div>

        @php
            $statusMap = [
                'pending'    => 1,
                'processing' => 2,
                'shipped'    => 3,
                'delivered'  => 4,
                'cancelled'  => 0,
            ];
            $currentStep = $statusMap[strtolower($order->status)] ?? 1;

            $steps = [
                ['icon' => 'bi-bag-check',    'label' => 'Order Placed',  'sub' => 'Received & logged'],
                ['icon' => 'bi-gear',         'label' => 'Processing',    'sub' => 'Being prepared'],
                ['icon' => 'bi-truck',        'label' => 'Shipped',       'sub' => 'In transit'],
                ['icon' => 'bi-box-seam',     'label' => 'Delivered',     'sub' => 'Package received'],
            ];
        @endphp

        @if($order->status === 'cancelled')
            <div class="alert alert-error">
                <i class="alert-icon bi bi-x-circle-fill"></i>
                <div>
                    <p class="font-bold">Order Cancelled</p>
                    <p class="text-xs mt-0.5">This order was cancelled and inventory stock has been restored.</p>
                </div>
            </div>
        @else
            <div class="flex items-start justify-between gap-2 overflow-x-auto pb-2">
                @foreach($steps as $i => $step)
                    @php
                        $stepNum   = $i + 1;
                        $completed = $stepNum < $currentStep;
                        $active    = $stepNum === $currentStep;
                        $pending   = $stepNum > $currentStep;
                    @endphp
                    <div class="flex flex-col items-center text-center flex-1 min-w-[70px] relative">

                        {{-- Connector line (not on last) --}}
                        @if($i < count($steps) - 1)
                            <div class="absolute top-4 left-[calc(50%+1.25rem)] right-[calc(-50%+1.25rem)] h-0.5 z-0
                                {{ $completed ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : 'bg-gray-800' }}">
                            </div>
                        @endif

                        {{-- Dot --}}
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm relative z-10 border-2
                            {{ $completed ? 'bg-emerald-500 border-emerald-400 text-gray-950 shadow-lg shadow-emerald-500/40' :
                               ($active    ? 'bg-gradient-to-r from-blue-600 to-indigo-500 border-blue-400 text-white shadow-lg shadow-blue-500/40 animate-pulse-glow' :
                                             'bg-gray-900 border-gray-700 text-gray-500') }}">
                            @if($completed)
                                <i class="bi bi-check-lg"></i>
                            @else
                                <i class="bi {{ $step['icon'] }}"></i>
                            @endif
                        </div>

                        {{-- Label --}}
                        <span class="text-[11px] font-bold mt-2 leading-tight
                            {{ $completed ? 'text-emerald-400' : ($active ? 'text-white' : 'text-gray-500') }}">
                            {{ $step['label'] }}
                        </span>
                        <span class="text-[9px] text-gray-600 mt-0.5">{{ $step['sub'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Current status badge --}}
            <div class="flex justify-center">
                <span class="badge {{ match(strtolower($order->status)) {
                    'pending'    => 'badge-amber',
                    'processing' => 'badge-blue',
                    'shipped'    => 'badge-indigo',
                    'delivered'  => 'badge-emerald',
                    default      => 'badge-cyan'
                } }} text-xs px-4 py-1.5">
                    <i class="bi bi-circle-fill text-[6px] animate-pulse"></i>
                    Current Status: {{ ucfirst($order->status) }}
                </span>
            </div>
        @endif
    </div>

    {{-- ── ORDER DETAILS GRID ── --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

        {{-- Items (8 cols) --}}
        <div class="md:col-span-8 space-y-4">
            <div class="glass-panel p-6 space-y-4">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider border-b border-gray-800/80 pb-3 flex items-center gap-2">
                    <i class="bi bi-box2 text-cyan-400"></i> Purchased Items
                </h3>
                <div class="divide-y divide-gray-800/60">
                    @foreach($order->items as $item)
                        <div class="py-3.5 flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-white">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    ${{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}
                                </p>
                            </div>
                            <span class="font-bold text-cyan-400 text-sm flex-shrink-0">
                                ${{ number_format($item->subtotal, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="border-t border-gray-800/80 pt-4 space-y-2 text-xs">
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-gray-300">
                            <span>Subtotal</span>
                            <span class="font-bold text-white">${{ number_format($order->subtotal_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-400 font-semibold">
                            <span>Discount ({{ $order->coupon_code }})</span>
                            <span>−${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-baseline pt-2 border-t border-gray-800/60">
                        <span class="text-sm font-bold text-white">Total Paid</span>
                        <span class="text-2xl font-black text-cyan-400">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery & Actions (4 cols) --}}
        <div class="md:col-span-4 space-y-4">
            <div class="glass-panel p-5 space-y-4 text-xs">
                <h3 class="font-bold text-white uppercase tracking-wider border-b border-gray-800/80 pb-2 flex items-center gap-2">
                    <i class="bi bi-geo-alt text-cyan-400"></i> Delivery Info
                </h3>
                <div class="space-y-2 text-gray-300">
                    <p class="font-bold text-white">{{ $order->customer_name }}</p>
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->city }}, {{ $order->postal_code }}</p>
                    <div class="pt-2 border-t border-gray-800/60 space-y-1.5">
                        <p><i class="bi bi-telephone text-cyan-400 mr-1.5"></i>{{ $order->customer_phone }}</p>
                        <p>
                            <i class="bi bi-credit-card text-cyan-400 mr-1.5"></i>
                            <span class="uppercase font-bold">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="space-y-2.5">
                <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank"
                    class="btn btn-ghost btn-full gap-2 border-gray-700 hover:border-cyan-500/50 hover:text-cyan-400">
                    <i class="bi bi-file-earmark-pdf"></i> Print Invoice
                </a>

                @if($order->isCancelable())
                    <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST"
                        onsubmit="return confirm('Cancel order {{ $order->order_number }}? Stock will be restored.');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-full gap-2">
                            <i class="bi bi-x-circle"></i> Cancel Order
                        </button>
                    </form>
                @endif

                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-full gap-2 text-xs">
                        <i class="bi bi-person-circle text-cyan-400"></i> View All My Orders
                    </a>
                @endauth

                <a href="{{ route('shop.index') }}" class="btn btn-primary btn-full btn-sm gap-2">
                    <i class="bi bi-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
