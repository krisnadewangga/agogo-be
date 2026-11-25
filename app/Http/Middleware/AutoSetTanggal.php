<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\TanggalHelper;

class AutoSetTanggal
{
    /**
     * Handle an incoming request.
     * Otomatis set tanggal produksi saat pergantian hari.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        TanggalHelper::autoSetTanggal();

        return $next($request);
    }
}
