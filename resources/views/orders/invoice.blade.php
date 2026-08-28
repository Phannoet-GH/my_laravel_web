@extends('layouts.app')

@section('title', 'Official Invoice - ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex justify-end mb-4">
        <button onclick="window.print()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-xl text-xs font-bold border border-gray-700">
            🖨️ Print / Download PDF Invoice
        </button>
    </div>

    <div class="glass-panel p-8 border-gray-800 space-y-6">
        <div class="flex justify-between items-start border-b border-gray-800 pb-6">
            <div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 text-white font-extrabold text-lg flex items-center justify-center mb-2">SE</div>
                <h2 class="text-xl font-bold text-white">SE Shop Systems</h2>
                <p class="text-xs text-gray-400">Engineered Hardware Systems Inc.</p>
            </div>
            <div class="text-right">
                <h1 class="text-2xl font-black text-white">INVOICE</h1>
                <p class="text-xs text-cyan-400 font-mono mt-1">Invoice #: {{ $order->order_number }}</p>
                <span class="inline-block px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 text-[10px] font-extrabold uppercase mt-2">
                    {{ $order->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 text-xs border-b border-gray-800 pb-6">
            <div>
                <span class="text-gray-400 font-bold uppercase block mb-1">Billed & Shipped To</span>
                <p class="font-bold text-white text-sm">{{ $order->customer_name }}</p>
                <p class="text-gray-300">{{ $order->shipping_address }}</p>
                <p class="text-gray-300">{{ $order->city }}, {{ $order->postal_code }}</p>
                <p class="text-gray-400 mt-1">Email: {{ $order->customer_email }}</p>
            </div>
            <div>
                <span class="text-gray-400 font-bold uppercase block mb-1">Payment Details</span>
                <p class="text-gray-300">Method: <strong class="text-white uppercase">{{ $order->payment_method ?? 'CARD' }}</strong></p>
                <p class="text-gray-300">Tracking Code: <strong class="text-cyan-400 font-mono">{{ $order->tracking_code ?? 'TRK-PENDING' }}</strong></p>
            </div>
        </div>

        <table class="w-full text-left text-xs text-gray-300">
            <thead class="bg-gray-900/80 text-gray-400 border-b border-gray-800 uppercase font-bold text-[10px]">
                <tr>
                    <th class="p-3">Item SKU</th>
                    <th class="p-3">Unit Price</th>
                    <th class="p-3">Qty</th>
                    <th class="p-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/60">
                @foreach($order->items as $item)
                    <tr>
                        <td class="p-3 font-bold text-white">{{ $item->product_name }}</td>
                        <td class="p-3">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="p-3">{{ $item->quantity }}</td>
                        <td class="p-3 text-right font-bold text-cyan-400">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="max-w-xs ml-auto space-y-2 text-xs pt-4 border-t border-gray-800">
            <div class="flex justify-between text-gray-400">
                <span>Subtotal:</span>
                <span class="font-bold text-white">${{ number_format($order->subtotal_amount ?? $order->total_amount, 2) }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-emerald-400 font-semibold">
                    <span>Discount:</span>
                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-gray-400">
                <span>Tax (5%):</span>
                <span class="font-bold text-white">${{ number_format($order->tax_amount ?? 0, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm font-bold text-white pt-2 border-t border-gray-800">
                <span>Total Paid:</span>
                <span class="text-xl font-black text-cyan-400">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
