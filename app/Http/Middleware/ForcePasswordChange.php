<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            $allowedRoutes = [
                'password.force_change',
                'password.force_change.update',
                'logout',
            ];

            $allowedPaths = [
                'force-change-password',
                'logout',
                'api/auth/force-change-password',
                'api/auth/logout',
            ];

            $currentRoute = $request->route()?->getName();
            $currentPath = trim($request->path(), '/');

            $isAllowed = in_array($currentRoute, $allowedRoutes) || in_array($currentPath, $allowedPaths);

            if (! $isAllowed) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'must_change_password' => true,
                        'message' => 'Default security credential detected. You must change your password before proceeding.',
                    ], 403);
                }

                return redirect()->route('password.force_change')
                    ->with('warning', 'Security requirement: You must change your default password on first login before accessing portal services.');
            }
        }

        return $next($request);
    }
}
