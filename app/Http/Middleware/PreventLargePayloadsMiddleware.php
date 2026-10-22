<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventLargePayloadsMiddleware
{
    /**
     * Maximum allowed request body size in bytes.
     * Default: 2MB (2,097,152 bytes) - sufficient for rich JSON/form payloads
     * while blocking memory-exhaustion and body-flooding DoS attacks.
     */
    protected int $maxPayloadBytes = 2097152;

    /**
     * Handle an incoming request and check payload size.
     */
    public function handle(Request $request, Closure $next, ?int $maxBytes = null): Response
    {
        $limit = $maxBytes ?? $this->maxPayloadBytes;

        // Check Content-Length header if provided
        $contentLength = $request->header('Content-Length');
        if ($contentLength !== null && (int) $contentLength > $limit) {
            return $this->payloadTooLargeResponse($request, $limit);
        }

        // Also check raw input length if present
        $rawContent = $request->getContent();
        if (is_string($rawContent) && strlen($rawContent) > $limit) {
            return $this->payloadTooLargeResponse($request, $limit);
        }

        return $next($request);
    }

    /**
     * Generate 413 Payload Too Large response.
     */
    protected function payloadTooLargeResponse(Request $request, int $limit): Response
    {
        $limitKb = round($limit / 1024);
        $message = "Request payload exceeds the maximum allowed limit of {$limitKb} KB.";

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'error' => 'Payload Too Large',
                'message' => $message,
            ], 413);
        }

        return response($message, 413);
    }
}
