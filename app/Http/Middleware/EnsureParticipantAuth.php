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
            // Jika login via auth web dan memiliki role peserta atau aktif dalam mode peserta
            if (auth()->check()) {
                $user = auth()->user();
                $available = $user->getAvailableRoles();
                $hasPesertaRole = $user->role === 'peserta' 
                    || in_array('Peserta', $available) 
                    || in_array('Peserta Diklat', $available);

                if ($hasPesertaRole) {
                    $peserta = \App\Models\Peserta::where('nip', $user->nik)
                        ->orWhere('email', $user->email)
                        ->first();

                    if (!$peserta) {
                        $peserta = \App\Models\Peserta::create([
                            'nip' => $user->nik ?: 'NIK-' . substr(md5($user->email), 0, 10),
                            'nama' => $user->name,
                            'email' => $user->email,
                            'jenis_kelamin' => 'Laki-laki',
                            'instansi' => 'PPSDM Kemnaker',
                        ]);
                    }

                    session(['participant_id' => $peserta->id]);
                    return $next($request);
                }
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
