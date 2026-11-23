<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply strict enterprise security headers.
     * Mencegah XSS, Clickjacking, MIME-sniffing, dan kebocoran data sensitif.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Mencegah website di-embed di iframe situs luar (Anti-Clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Mencegah browser menebak MIME-type berkas yang diunggah (Anti MIME-Sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Mengaktifkan filter XSS bawaan browser modern
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Membatasi pengiriman URL referer ke pihak ketiga (Anti Referrer Leakage)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Batasi akses perangkat keras (Permissions Policy)
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=()');

        // 6. Untuk rute terotentikasi (admin & peserta), cegah penyimpanan cache publik yang sensitif
        if (auth()->check() || session()->has('participant_id')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
