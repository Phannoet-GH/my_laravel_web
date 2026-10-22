<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInputsMiddleware
{
    /**
     * Field names that should be exempted from sanitization (passwords).
     *
     * @var array<int, string>
     */
    protected array $except = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'new_password_confirmation',
    ];

    /**
     * Handle an incoming request and sanitize inputs against XSS and injection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        if (!empty($input)) {
            $sanitized = $this->cleanArray($input);
            $request->replace($sanitized);
        }

        return $next($request);
    }

    /**
     * Recursively sanitize input array.
     */
    protected function cleanArray(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->except, true)) {
                $clean[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $clean[$key] = $this->cleanArray($value);
            } elseif (is_string($value)) {
                $clean[$key] = $this->cleanString($value);
            } else {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }

    /**
     * Clean a single string against null bytes, control characters, and dangerous XSS vectors.
     */
    protected function cleanString(string $value): string
    {
        // 1. Remove null bytes (\0)
        $value = str_replace(chr(0), '', $value);

        // 2. Remove script tags and their contents
        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value) ?? $value;

        // 3. Remove iframe tags and their contents
        $value = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $value) ?? $value;

        // 4. Remove javascript: pseudo-protocol URIs
        $value = preg_replace('/javascript:[^"]*/i', '', $value) ?? $value;

        // 5. Remove dangerous inline event handlers (onerror=, onload=, onclick=, etc.)
        $value = preg_replace('/\s*on\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $value) ?? $value;

        // 6. Ensure valid UTF-8
        $cleanUtf8 = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        return is_string($cleanUtf8) ? $cleanUtf8 : $value;
    }
}
