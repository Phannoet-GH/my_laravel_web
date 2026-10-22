<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply hardened HTTP security headers.
     *
     * Protects against:
     * - Clickjacking (X-Frame-Options)
     * - MIME-confusion & XSS sniffing (X-Content-Type-Options)
     * - Reflected XSS (X-XSS-Protection)
     * - Cross-Site Scripting & Injection (Content-Security-Policy)
     * - Unintended information leakage (Referrer-Policy)
     * - Hardware & browser capability abuse (Permissions-Policy)
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Frame options - Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Legacy XSS filter for older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy
        $response->headers->set(
            'Permissions-Policy',
            'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()'
        );

        // Content Security Policy (CSP)
        // Allow scripts and styles from self, CDNs (jsdelivr, google fonts), and inline styles/scripts
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net data:",
            "img-src 'self' data: https: blob:",
            "connect-src 'self'",
            "frame-ancestors 'self'",
            "form-action 'self'",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        // Enforce HSTS if running over HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
