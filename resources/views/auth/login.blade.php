@extends('layouts.app')

@section('title', 'Sign In — SE Shop')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-16 px-4 relative overflow-hidden">

    {{-- Ambient Background --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-1/4 left-1/3 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 right-1/4 w-[400px] h-[400px] bg-indigo-600/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        {{-- Auth Box Card --}}
        <div class="auth-box space-y-7">

            {{-- Logo + Title --}}
            <div class="text-center space-y-3">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 text-white font-black text-2xl shadow-xl shadow-blue-500/30 hover:scale-105 transition-transform">
                    SE
                </a>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight">Welcome back</h1>
                    <p class="text-sm text-gray-400 mt-1">Sign in to your SE Shop account</p>
                </div>
            </div>

            {{-- Errors --}}
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

            {{-- Intended redirect hint --}}
            @if(session('intended_message'))
                <div class="alert alert-info animate-slide-in-up">
                    <i class="alert-icon bi bi-info-circle-fill"></i>
                    <p>{{ session('intended_message') }}</p>
                </div>
            @endif

            {{-- Login Form --}}
            <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-icon-group">
                        <span class="input-icon-left">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email" required autocomplete="email"
                            value="{{ old('email') }}"
                            placeholder="you@example.com"
                            class="input-dark {{ $errors->has('email') ? 'border-rose-500/70 focus:border-rose-500' : '' }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-icon-group">
                        <span class="input-icon-left">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                            placeholder="••••••••"
                            class="input-dark has-right-icon">
                        <button type="button" id="togglePwd" class="input-icon-right" title="Toggle Password Visibility">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-400 hover:text-gray-300 select-none">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded bg-gray-950 border-gray-700 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-0">
                        Keep me signed in
                    </label>
                </div>

                <button type="submit" id="submitBtn"
                    class="btn btn-primary btn-full btn-lg gap-2.5 group">
                    <span id="btnText">Sign In to Account</span>
                    <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform" id="btnIcon"></i>
                    <span id="btnSpinner" class="spinner hidden"></span>
                </button>

                <p class="text-center text-xs text-gray-500">
                    New to SE Shop?
                    <a href="{{ route('register') }}" class="text-cyan-400 font-semibold hover:text-cyan-300 ml-1">Create a free account →</a>
                </p>
            </form>

            {{-- Trust Badges --}}
            <div class="flex items-center justify-center gap-6 pt-3 border-t border-gray-800/60">
                <span class="flex items-center gap-2 text-[11px] text-gray-400">
                    <i class="bi bi-shield-check text-emerald-400 text-xs"></i> SSL Secured
                </span>
                <span class="flex items-center gap-2 text-[11px] text-gray-400">
                    <i class="bi bi-lock text-cyan-400 text-xs"></i> Encrypted
                </span>
                <span class="flex items-center gap-2 text-[11px] text-gray-400">
                    <i class="bi bi-person-check text-blue-400 text-xs"></i> RBAC Protected
                </span>
            </div>

        </div>
    </div>
</div>

<script>
    // Password visibility toggle
    document.getElementById('togglePwd')?.addEventListener('click', () => {
        const pw   = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (pw.type === 'password') {
            pw.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pw.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });

    // Loading state on submit
    document.getElementById('loginForm')?.addEventListener('submit', () => {
        document.getElementById('btnText').textContent = 'Signing in...';
        document.getElementById('btnIcon').classList.add('hidden');
        document.getElementById('btnSpinner').classList.remove('hidden');
        document.getElementById('submitBtn').disabled = true;
    });
</script>
@endsection
