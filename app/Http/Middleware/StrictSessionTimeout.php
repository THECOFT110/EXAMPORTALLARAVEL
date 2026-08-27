<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StrictSessionTimeout
{
    /**
     * Handle an incoming request and enforce strict 15-minute inactivity session expiration.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity_timestamp');
            $timeoutSeconds = 15 * 60; // Strict 15 minutes (900s)

            if ($lastActivity && (time() - $lastActivity > $timeoutSeconds)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your session has expired due to 15 minutes of inactivity.',
                        'reason' => 'inactivity',
                    ], 401);
                }

                return redirect()->route('login', ['reason' => 'inactivity'])
                    ->with('error', 'Your session expired due to 15 minutes of inactivity. Please sign in again.');
            }

            // Update timestamp on every active request
            session(['last_activity_timestamp' => time()]);
        }

        return $next($request);
    }
}
