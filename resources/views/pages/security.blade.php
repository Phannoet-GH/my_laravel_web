@extends('layouts.app')

@section('title', 'Security Infrastructure — SE Shop')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">

    {{-- Header Control Box --}}
    <div class="glass-panel p-8 border-emerald-500/30 space-y-3 relative overflow-hidden">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider">
            <i class="bi bi-shield-lock-fill"></i> Enterprise Security Architecture
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">Security & Infrastructure</h1>
        <p class="text-sm text-gray-400 max-w-2xl">
            SE Shop is engineered with defense-in-depth security standards to protect customer accounts, order transactions, and administrative controls.
        </p>
        <span class="text-[11px] text-gray-500 font-mono block pt-2">System Status: All Security Layers Active</span>
    </div>

    {{-- Security Metrics Grid (4 Stat Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-metric-card">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <div>
                    <div class="text-lg font-black text-white">256-Bit SSL</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase">TLS Encryption</div>
                </div>
            </div>
        </div>

        <div class="stat-metric-card">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div class="text-lg font-black text-white">CSRF Token</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Request Shield</div>
                </div>
            </div>
        </div>

        <div class="stat-metric-card">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                    <div class="text-lg font-black text-white">RBAC Strict</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Role Isolation</div>
                </div>
            </div>
        </div>

        <div class="stat-metric-card">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-400 flex items-center justify-center text-lg flex-shrink-0">
                    <i class="bi bi-key-fill"></i>
                </div>
                <div>
                    <div class="text-lg font-black text-white">Bcrypt 12</div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Password Hashing</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Safeguards --}}
    <div class="space-y-6">

        <div class="glass-panel p-6 sm:p-8 space-y-4">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center font-bold text-sm">1</div>
                <h2 class="text-lg font-bold text-white">Authentication & Access Control</h2>
            </div>
            <p class="text-xs text-gray-300 leading-relaxed">
                User passwords are key-stretched using Bcrypt (cost factor 12) with unique salts. Administrative routes (`/admin/*`) are protected by dual-layer middleware enforcing strict <strong class="text-white">Role-Based Access Control (RBAC)</strong> to ensure customer accounts cannot access merchant fulfillment panels.
            </p>
        </div>

        <div class="glass-panel p-6 sm:p-8 space-y-4">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-3">
                <div class="w-8 h-8 rounded-xl bg-cyan-500/15 text-cyan-400 flex items-center justify-center font-bold text-sm">2</div>
                <h2 class="text-lg font-bold text-white">Database & Query Protection</h2>
            </div>
            <p class="text-xs text-gray-300 leading-relaxed">
                All database interactions utilize parameterized prepared statements via Eloquent ORM and PDO, insulating the application against SQL Injection (SQLi) attacks. User input across forms and checkout steps undergo strict sanitization and validation.
            </p>
        </div>

        <div class="glass-panel p-6 sm:p-8 space-y-4">
            <div class="flex items-center gap-3 border-b border-gray-800 pb-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/15 text-indigo-400 flex items-center justify-center font-bold text-sm">3</div>
                <h2 class="text-lg font-bold text-white">Vulnerability Reporting (Bug Bounty)</h2>
            </div>
            <p class="text-xs text-gray-300 leading-relaxed">
                We welcome responsible disclosure of potential security vulnerabilities. If you discover a security flaw or vulnerability in SE Shop, please email our engineering response team at <strong class="text-cyan-400 font-mono">security@eshop.com</strong>.
            </p>
        </div>

    </div>

</div>
@endsection
