@extends('layouts.app')

@section('title', 'Shipping & Delivery Policy - SE Shop')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10 text-center">
        <span class="px-3 py-1 rounded-full text-xs font-mono font-semibold bg-blue-900/50 text-blue-300 border border-blue-500/30 uppercase tracking-widest">Global Logistics</span>
        <h1 class="text-4xl font-extrabold text-white mt-4 tracking-tight">Shipping & Delivery Policy</h1>
        <p class="text-gray-400 text-sm mt-2 max-w-xl mx-auto">Fast, secure, and insured worldwide delivery engineered for developer hardware and high-value tech.</p>
    </div>

    <div class="space-y-8 text-gray-300 text-sm leading-relaxed">

        <!-- Express Worldwide Shipping Box -->
        <div class="p-6 rounded-2xl bg-gradient-to-r from-blue-950/60 to-indigo-950/60 border border-blue-500/30 flex items-start gap-4">
            <div class="p-3 rounded-xl bg-blue-600/20 text-cyan-400 border border-blue-500/40 shrink-0">
                <i class="bi bi-truck text-2xl"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white mb-1">Free Express Worldwide Shipping on Orders $500+</h3>
                <p class="text-xs text-gray-300">Use promo code <span class="font-mono text-cyan-400 bg-gray-900 px-1.5 py-0.5 rounded border border-gray-700">SESHOP2026</span> at checkout for complimentary priority air freight on all eligible orders over $500 USD.</p>
            </div>
        </div>

        <!-- Section 1 -->
        <div class="p-6 rounded-2xl bg-gray-900/60 border border-gray-800 space-y-3">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-lightning-charge text-cyan-400"></i> Order Processing & Dispatch Timelines
            </h2>
            <p>
                All in-stock orders are processed, quality-inspected, and dispatched within <strong>24 business hours</strong> from our central logistics hubs (North America, Europe, and Asia-Pacific).
            </p>
            <ul class="list-disc pl-5 space-y-1 text-xs text-gray-400">
                <li>Orders placed before 2:00 PM EST (Monday–Friday) ship the same business day.</li>
                <li>Customized builds (e.g. pre-lubed mechanical keyboards or custom storage upgrades) require 1 additional business day for assembly & testing.</li>
                <li>Tracking links are dispatched automatically via email and available in your <a href="{{ route('orders.lookup') }}" class="text-cyan-400 hover:underline">Order Tracking Portal</a>.</li>
            </ul>
        </div>

        <!-- Section 2: Delivery Methods & Rates -->
        <div class="p-6 rounded-2xl bg-gray-900/60 border border-gray-800 space-y-4">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-[#080b12] bi-box-seam text-cyan-400"></i> Delivery Methods & Shipping Rates
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-300">
                    <thead class="bg-gray-800/80 text-gray-200 uppercase font-mono text-[11px]">
                        <tr>
                            <th class="p-3 rounded-l-lg">Shipping Tier</th>
                            <th class="p-3">Estimated Time</th>
                            <th class="p-3">Cost (Under $500)</th>
                            <th class="p-3 rounded-r-lg">Cost ($500+)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60">
                        <tr>
                            <td class="p-3 font-semibold text-white">Standard Ground Courier</td>
                            <td class="p-3">3 – 5 Business Days</td>
                            <td class="p-3 text-cyan-400 font-mono">$15.00</td>
                            <td class="p-3 text-emerald-400 font-bold font-mono">FREE</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold text-white">Priority Air Express (DHL/FedEx)</td>
                            <td class="p-3">1 – 2 Business Days</td>
                            <td class="p-3 text-cyan-400 font-mono">$29.99</td>
                            <td class="p-3 text-cyan-400 font-mono">$14.99</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold text-white">International Duty-Paid Air Freight</td>
                            <td class="p-3">3 – 7 Business Days</td>
                            <td class="p-3 text-cyan-400 font-mono">$45.00</td>
                            <td class="p-3 text-emerald-400 font-bold font-mono">FREE (Code)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 3: Package Protection & Signature Requirement -->
        <div class="p-6 rounded-2xl bg-gray-900/60 border border-gray-800 space-y-3">
            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-shield-check text-emerald-400"></i> Full Transit Insurance & Signature Delivery
            </h2>
            <p>
                Every high-value hardware shipment (laptops, OLED displays, workstations) automatically includes full transit insurance covering loss, theft, or damage during transport.
            </p>
            <p class="text-xs text-gray-400">
                To prevent porch piracy, shipments valued over $400 USD require an adult signature upon delivery. If you are unavailable, your courier will re-attempt delivery or hold your parcel at a local pickup point.
            </p>
        </div>

    </div>
</div>
@endsection
