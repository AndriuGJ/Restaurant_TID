<?php

namespace App\Http\Middleware;

use App\Models\Restaurant\CashRegisterSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePosSessionOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $hasOpenSession = CashRegisterSession::where('status', 'open')->exists();

        if (! $hasOpenSession) {
            return redirect()->route('pos.requires-session');
        }

        return $next($request);
    }
}
