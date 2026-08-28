@extends('layouts.app')

@section('title', 'Privacy Policy — SE Shop')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">

    {{-- Header Control Box --}}
    <div class="glass-panel p-8 border-cyan-500/30 space-y-3 relative overflow-hidden">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 text-xs font-bold uppercase tracking-wider">
            <i class="bi bi-shield-check"></i> Data Protection & Transparency
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Privacy Policy</h1>
        <p class="text-sm text-gray-400 max-w-2xl">
            At SE Shop, your data privacy and security are fundamental principles. This Privacy Policy details how we collect, process, and protect your personal information in compliance with GDPR and global privacy standards.
        </p>
        <span class="text-[11px] text-gray-500 font-mono block pt-2">Last Updated: August 28, 2026</span>
    </div>

    {{-- Content Sections Grid --}}
    <div class="space-y-6">

        {{-- Section 1 --}}
        <div class="glass-panel p-6 sm:p-8 space-y-4">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-3">
                <div class="w-8 h-8 rounded-xl bg-cyan-500/15 text-cyan-400 flex items-center justify-center font-bold text-sm">1</div>
                <h2 class="text-lg font-bold text-white">Information We Collect</h2>
            </div>
            <p class="text-xs text-gray-300 leading-relaxed">
                When you create an account, purchase hardware, or interact with our merchant services, we collect necessary customer information:
            </p>
            <ul class="space-y-2 text-xs text-gray-400 list-disc list-inside pl-2">
                <li><strong class="text-white">Account Data:</strong> Full name, email address, password hashes (Bcrypt 12 rounds), and account preferences.</li>
                <li><strong class="text-white">Order & Fulfillment Data:</strong> Shipping street address, city, postal code, phone number, and order item details.</li>
                <li><strong class="text-white">Payment Identifiers:</strong> Tokenized payment transaction IDs processed securely via SSL encryption (card numbers are never stored on our servers).</li>
                <li><strong class="text-white">Technical Logs:</strong> IP address, user agent, session identifiers, and CSRF protection tokens.</li>
            </ul>
        </div>

        {{-- Section 2 --}}
        <div class="glass-panel p-6 sm:p-8 space-y-4">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-3">
                <div class="w-8 h-8 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center font-bold text-sm">2</div>
                <h2 class="text-lg font-bold text-white">How We Use Your Information</h2>
            </div>
            <p class="text-xs text-gray-300 leading-relaxed">
                Your data is processed strictly to deliver e-commerce functionality and maintain platform integrity:
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 space-y-1">
                    <h3 class="text-xs font-bold text-cyan-400"><i class="bi bi-box-seam mr-1.5"></i> Order Fulfillment</h3>
                    <p class="text-[11px] text-gray-400">Processing hardware orders, generating invoices, and providing tracking updates.</p>
                </div>
                <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 space-y-1">
                    <h3 class="text-xs font-bold text-indigo-400"><i class="bi bi-shield-lock mr-1.5"></i> Fraud Prevention</h3>
                    <p class="text-[11px] text-gray-400">Detecting unauthorized transactions and maintaining role-based access control (RBAC).</p>
                </div>
                <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 space-y-1">
                    <h3 class="text-xs font-bold text-purple-400"><i class="bi bi-envelope mr-1.5"></i> Customer Support</h3>
                    <p class="text-[11px] text-gray-400">Sending order confirmations, warranty notifications, and responding to support tickets.</p>
                </div>
                <div class="p-4 rounded-xl bg-gray-950/60 border border-gray-800 space-y-1">
                    <h3 class="text-xs font-bold text-emerald-400"><i class="bi bi-sliders mr-1.5"></i> Platform Performance</h3>
                    <p class="text-[11px] text-gray-400">Optimizing shop inventory indexing, cart operations, and session persistence.</p>
                </div>
            </div>
        </div>

        {{-- Section 3 --}}
        <div class="glass-panel p-6 sm:p-8 space-y-4">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center font-bold text-sm">3</div>
                <h2 class="text-lg font-bold text-white">Data Retention & Security Safeguards</h2>
            </div>
            <p class="text-xs text-gray-300 leading-relaxed">
                We store account data securely in PostgreSQL/MySQL databases with active connection encryption. Customer records are retained only for active account lifetimes and tax compliance mandates. You may request account deletion or data exports at any time by contacting privacy@eshop.com.
            </p>
        </div>

    </div>

</div>
@endsection
