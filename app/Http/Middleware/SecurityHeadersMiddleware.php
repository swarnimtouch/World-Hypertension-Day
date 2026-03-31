<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        $nonce = base64_encode(random_bytes(16));
        view()->share('cspNonce', $nonce);
        $response = $next($request);

        // Clickjacking protection
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // HSTS (HTTPS only)
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        // Content Security Policy (safe baseline)


        $csp = "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' 'nonce-{$nonce}' https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net https://cdn.zingchart.com https://maxcdn.bootstrapcdn.com https://stackpath.bootstrapcdn.com https://ajax.googleapis.com https://www.shieldui.com https://unpkg.com; "
            . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.datatables.net https://code.ionicframework.com https://maxcdn.bootstrapcdn.com https://code.jquery.com https://stackpath.bootstrapcdn.com https://pro.fontawesome.com https://www.shieldui.com https://cdn.jsdelivr.net; "
            . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://code.ionicframework.com https://maxcdn.bootstrapcdn.com https://stackpath.bootstrapcdn.com https://pro.fontawesome.com https://www.shieldui.com; "
            . "img-src 'self' data: blob: https://swarnimpolling.s3.ap-south-1.amazonaws.com; "
            . "connect-src 'self' https://cdnjs.cloudflare.com https://stackpath.bootstrapcdn.com https://cdn.jsdelivr.net https://code.jquery.com; "
            . "frame-src https://www.youtube.com https://www.youtube-nocookie.com; "
            . "object-src 'none'; "
            . "frame-ancestors 'self'; "
            . "base-uri 'self'; "
            . "form-action 'self'";

        $response->headers->set('Content-Security-Policy', $csp);

        // Referrer policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');

        return $response;
    }
}
