<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsurePelanggan
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isPelanggan()) {
            return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Anda bukan pelanggan.');
        }

        return $next($request);
    }
}
