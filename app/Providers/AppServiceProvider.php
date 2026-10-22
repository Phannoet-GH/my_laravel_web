<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Global API rate limiter (60 requests/minute)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Authentication rate limiter: 5 attempts per minute per IP + email
        // Mitigates credential stuffing, password brute-forcing, and auth endpoint DoS
        RateLimiter::for('auth', function (Request $request) {
            $email = $request->input('email') ? strtolower(trim((string) $request->input('email'))) : 'guest';
            $key = 'auth:' . $email . '|' . $request->ip();

            return Limit::perMinute(5)->by($key)->response(function (Request $request, array $headers) {
                $seconds = $headers['Retry-After'] ?? 60;
                $msg = "Too many authentication attempts. Please try again in {$seconds} seconds.";

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Too Many Requests',
                        'message' => $msg,
                        'retry_after' => (int) $seconds,
                    ], 429, $headers);
                }

                return back()
                    ->withInput($request->except('password', 'password_confirmation', 'current_password', 'new_password'))
                    ->withErrors(['email' => $msg]);
            });
        });

        // Checkout & Orders rate limiter: 10 requests per minute
        // Mitigates card testing, checkout flooding, and inventory lockup attacks
        RateLimiter::for('orders', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())->response(function (Request $request, array $headers) {
                $seconds = $headers['Retry-After'] ?? 60;
                $msg = "Too many order requests. Please wait {$seconds} seconds before attempting another order.";

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Too Many Requests',
                        'message' => $msg,
                        'retry_after' => (int) $seconds,
                    ], 429, $headers);
                }

                return back()->with('error', $msg);
            });
        });

        // Order lookup rate limiter: 10 requests per minute
        // Mitigates order number enumeration and customer PII scraping
        RateLimiter::for('lookup', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())->response(function (Request $request, array $headers) {
                $seconds = $headers['Retry-After'] ?? 60;
                $msg = "Too many lookup attempts. Please wait {$seconds} seconds before searching again.";

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Too Many Requests',
                        'message' => $msg,
                        'retry_after' => (int) $seconds,
                    ], 429, $headers);
                }

                return back()->with('error', $msg);
            });
        });

        // Product review submission rate limiter: 5 submissions per minute
        // Mitigates review spamming and comment flooding
        RateLimiter::for('reviews', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip())->response(function (Request $request, array $headers) {
                $seconds = $headers['Retry-After'] ?? 60;
                $msg = "Too many reviews submitted. Please wait {$seconds} seconds before posting again.";

                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Too Many Requests',
                        'message' => $msg,
                        'retry_after' => (int) $seconds,
                    ], 429, $headers);
                }

                return back()->with('error', $msg);
            });
        });

        // Cart operations rate limiter: 30 operations per minute
        RateLimiter::for('cart', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // General Web rate limiter: 120 requests per minute per IP
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });
    }
}

