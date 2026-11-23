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
            // Jika login via auth web dengan role peserta, sambungkan sesi otomatis
            if (auth()->check() && auth()->user()->role === 'peserta') {
                $user = auth()->user();
                $peserta = \App\Models\Peserta::where('nip', $user->nik)
                    ->orWhere('email', $user->email)
                    ->first();

                if (!$peserta) {
                    $peserta = \App\Models\Peserta::create([
                        'nip' => $user->nik,
                        'nama' => $user->name,
                        'email' => $user->email,
                        'jenis_kelamin' => 'Laki-laki',
                        'instansi' => 'PPSDM Kemnaker',
                    ]);
                }

                session(['participant_id' => $peserta->id]);
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi login peserta telah berakhir.'], 401);
            }
            session()->put('url.intended_participant', $request->fullUrl());
            return redirect()->route('portal.login')->with('warning', 'Silakan masuk menggunakan Kode Peserta dan PIN Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
