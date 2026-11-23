<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParticipantAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('participant_id')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi login peserta telah berakhir.'], 401);
            }
            return redirect()->route('portal.login')->with('warning', 'Silakan masuk menggunakan Kode Peserta dan PIN Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
